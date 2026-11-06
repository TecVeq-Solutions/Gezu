<?php

namespace App\Services;

use App\Models\AuthSetting;
use App\Models\Campaign;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Log;

class StripeCheckoutService
{
    /**
     * Create a Stripe Hosted Checkout Session for Brand Campaign Funding.
     */
    public function createCampaignCheckoutSession(Campaign $campaign, User $brandUser): ?string
    {
        if (! class_exists(\Stripe\StripeClient::class) && file_exists(base_path('vendor/stripe/stripe-php/init.php'))) {
            require_once base_path('vendor/stripe/stripe-php/init.php');
        }

        $mode = AuthSetting::get('stripe_mode', 'test');
        if ($mode === 'live') {
            $secretKey = AuthSetting::get('stripe_live_secret_key') 
                ?: AuthSetting::get('stripe_secret_key') 
                ?: config('services.stripe.secret') 
                ?: env('STRIPE_SECRET_KEY') 
                ?: env('STRIPE_SECRET');
        } else {
            $secretKey = AuthSetting::get('stripe_test_secret_key') 
                ?: AuthSetting::get('stripe_secret_key') 
                ?: config('services.stripe.secret') 
                ?: env('STRIPE_SECRET_KEY') 
                ?: env('STRIPE_SECRET');
        }

        if (! $secretKey || $secretKey === '••••••••••••••••') {
            session()->flash('stripe_error', 'Stripe secret key is not configured for ' . strtoupper($mode) . ' mode. Please check Admin -> Settings -> Stripe.');
            return null;
        }

        $budgetAmountCents = max(100, (int) round(($campaign->total_budget ?? 100) * 100));
        $successUrl = url("/brand/campaigns/deposit-success?session_id={CHECKOUT_SESSION_ID}&campaign_id={$campaign->id}");
        $cancelUrl = url("/brand/campaigns?unfunded={$campaign->id}");

        // Standard Stripe Checkout parameters (Dynamic Payment Methods enabled)
        $sessionParams = [
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => "GEZUX Campaign Bounty Deposit: {$campaign->title}",
                            'description' => "Guaranteed bounty pool for verified short-form video clip distributions.",
                        ],
                        'unit_amount' => $budgetAmountCents,
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'customer_email' => $brandUser->email,
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'campaign_id' => (string) $campaign->id,
                'brand_id' => (string) $campaign->brand_id,
                'user_id' => (string) $brandUser->id,
                'type' => 'campaign_deposit',
            ],
        ];

        $postData = [
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'customer_email' => $brandUser->email,
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => "GEZUX Campaign Bounty Deposit: {$campaign->title}",
                            'description' => "Guaranteed bounty pool for verified short-form video clip distributions.",
                        ],
                        'unit_amount' => $budgetAmountCents,
                    ],
                    'quantity' => 1,
                ],
            ],
            'metadata' => [
                'campaign_id' => (string) $campaign->id,
                'brand_id' => (string) $campaign->brand_id,
                'user_id' => (string) $brandUser->id,
                'type' => 'campaign_deposit',
            ],
        ];

        try {
            // Direct cURL with safe timeouts and SSL resilience
            $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $secretKey,
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

            $res = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr = curl_error($ch);
            curl_close($ch);

            if ($curlErr) {
                Log::error('Stripe cURL Network Error: ' . $curlErr);
                session()->flash('stripe_error', 'Stripe Network Error: ' . $curlErr);
                session()->flash('error', 'Stripe Network Error: ' . $curlErr);
                return null;
            }

            $json = json_decode($res, true);
            if ($httpCode >= 200 && $httpCode < 300 && !empty($json['url'])) {
                return $json['url'];
            }

            $errMsg = $json['error']['message'] ?? ('HTTP ' . $httpCode . ': ' . substr($res, 0, 300));
            Log::error('Stripe API error: ' . $errMsg);
            session()->flash('stripe_error', 'Stripe API Error: ' . $errMsg);
            session()->flash('error', 'Stripe API Error: ' . $errMsg);
            return null;
        } catch (\Throwable $e) {
            Log::error('Stripe Checkout Exception: ' . $e->getMessage());
            session()->flash('stripe_error', 'Stripe Exception: ' . $e->getMessage());
            session()->flash('error', 'Stripe Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Handle payment completion callback and credit transaction & campaign.
     */
    public function handleSuccessfulPayment(string $sessionId, ?Campaign $campaign = null): array
    {
        $mode = AuthSetting::get('stripe_mode', 'test');
        if ($mode === 'live') {
            $secretKey = AuthSetting::get('stripe_live_secret_key') 
                ?: AuthSetting::get('stripe_secret_key') 
                ?: config('services.stripe.secret') 
                ?: env('STRIPE_SECRET_KEY') 
                ?: env('STRIPE_SECRET');
        } else {
            $secretKey = AuthSetting::get('stripe_test_secret_key') 
                ?: AuthSetting::get('stripe_secret_key') 
                ?: config('services.stripe.secret') 
                ?: env('STRIPE_SECRET_KEY') 
                ?: env('STRIPE_SECRET');
        }

        try {
            $amountPaid = 0;
            $campaignId = $campaign?->id;
            $userId = $campaign?->brand?->user_id;
            $paymentStatus = null;

            // Direct resilient cURL retrieval
            $ch = curl_init("https://api.stripe.com/v1/checkout/sessions/" . urlencode($sessionId));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $secretKey,
            ]);

            $res = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr = curl_error($ch);
            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                $data = json_decode($res, true);
                $paymentStatus = $data['payment_status'] ?? null;
                $amountPaid = ($data['amount_total'] ?? 0) / 100;
                $campaignId = $data['metadata']['campaign_id'] ?? $campaignId;
                $userId = $data['metadata']['user_id'] ?? $userId;
            } else {
                Log::error("Stripe Session Retrieval Failed ($httpCode): $res");
            }

            // CRITICAL SECURITY CHECK: Prevent unpaid session exploits
            if ($paymentStatus !== 'paid') {
                Log::warning("GEZUX Stripe Security: Attempted checkout completion with unpaid status '{$paymentStatus}' for session {$sessionId}");
                return [
                    'success' => false, 
                    'message' => "Payment has not been settled (Current status: {$paymentStatus})."
                ];
            }

            if (! $campaign && $campaignId) {
                $campaign = Campaign::find($campaignId);
            }

            if ($campaign) {
                $campaign->update([
                    'status' => 'active',
                ]);
            }

            $user = User::find($userId);
            if ($user) {
                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'currency' => 'USD',
                        'available_balance' => 0.00,
                        'pending_balance' => 0.00,
                        'locked_balance' => 0.00,
                        'total_withdrawn' => 0.00,
                    ]
                );

                $depositAmount = $amountPaid > 0 ? $amountPaid : (float)($campaign?->total_budget ?? 0);

                // Record transaction in immutable ledger (idempotent via unique reference_id)
                \App\Models\WalletTransaction::firstOrCreate(
                    ['reference_id' => 'STRIPE_SESSION_' . $sessionId],
                    [
                        'wallet_id' => $wallet->id,
                        'type' => 'campaign_funding',
                        'amount' => $depositAmount,
                        'currency' => 'USD',
                        'status' => 'completed',
                        'description' => "Stripe Checkout Deposit: " . ($campaign?->title ?? 'Campaign Bounty Pool'),
                    ]
                );
            }

            return ['success' => true, 'amount' => $amountPaid, 'campaign' => $campaign];
        } catch (\Throwable $e) {
            Log::error('Stripe Verification Error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
