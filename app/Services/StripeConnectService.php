<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripeConnectService
{
    /**
     * Start Stripe Connect Express onboarding flow for a creator.
     */
    public static function initiateOnboarding(User $user, string $country = 'US'): array
    {
        $wallet = $user->wallet ?? Wallet::create(['user_id' => $user->id, 'currency' => 'USD']);

        $secretKey = config('services.stripe.secret') ?? env('STRIPE_SECRET');
        $accountId = $wallet->stripe_account_id;

        // Simulated local development mode if API key is not set
        if (!$secretKey) {
            if (!$accountId) {
                $accountId = 'acct_demo_' . substr(md5($user->id . time()), 0, 16);
                $wallet->update([
                    'stripe_account_id' => $accountId,
                    'payouts_enabled' => true,
                    'stripe_onboarding_completed' => true,
                    'stripe_connected_at' => now(),
                ]);
            }

            return [
                'mode' => 'simulated',
                'account_id' => $accountId,
                'url' => '/clipper/payment-setup?onboarding=complete',
                'message' => 'Stripe Connect simulated in development environment.',
            ];
        }

        try {
            // 1. Create Connected Account if not exists
            if (!$accountId) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $secretKey,
                ])->asForm()->post('https://api.stripe.com/v1/accounts', [
                    'type' => 'express',
                    'country' => strtoupper($country),
                    'email' => $user->email,
                    'capabilities' => [
                        'transfers' => ['requested' => 'true'],
                    ],
                    'business_type' => 'individual',
                    'metadata' => [
                        'user_id' => (string) $user->id,
                        'platform' => 'GEZUX',
                    ],
                ]);

                if (!$response->successful()) {
                    throw new Exception("Stripe Account Creation Failed: " . ($response->json('error.message') ?? $response->body()));
                }

                $accountId = $response->json('id');
                $wallet->update(['stripe_account_id' => $accountId]);
            }

            // 2. Create Account Link for onboarding redirect
            $linkResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $secretKey,
            ])->asForm()->post('https://api.stripe.com/v1/account_links', [
                'account' => $accountId,
                'refresh_url' => url('/clipper/payment-setup?refresh=true'),
                'return_url' => url('/clipper/payment-setup?success=true'),
                'type' => 'account_onboarding',
            ]);

            if (!$linkResponse->successful()) {
                throw new Exception("Stripe Account Link Failed: " . ($linkResponse->json('error.message') ?? $linkResponse->body()));
            }

            return [
                'mode' => 'live',
                'account_id' => $accountId,
                'url' => $linkResponse->json('url'),
            ];
        } catch (\Throwable $e) {
            Log::error("Stripe onboarding error for user {$user->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Compute comprehensive Stripe Connect account readiness.
     */
    public static function getAccountReadiness(User $user): array
    {
        $wallet = $user->wallet;
        $profile = $user->creatorProfile;

        $accountId = $wallet?->stripe_account_id;
        $hasAccount = !empty($accountId);
        $payoutsEnabled = (bool) ($wallet?->payouts_enabled ?? false);

        // Mask account ID for privacy (e.g. acct_1N9xxxxxxxxx)
        $maskedId = null;
        if ($hasAccount) {
            $prefix = substr($accountId, 0, 8);
            $maskedId = $prefix . '••••••••';
        }

        $checklist = [
            [
                'title' => 'Email Verified',
                'description' => 'Security confirmation for account ownership',
                'completed' => $user->email_verified_at !== null,
            ],
            [
                'title' => 'Creator Profile Setup',
                'description' => 'Display name and country configured',
                'completed' => $profile !== null && !empty($profile->display_name),
            ],
            [
                'title' => 'Stripe Account Connected',
                'description' => 'Stripe Express marketplace identity registered',
                'completed' => $hasAccount,
            ],
            [
                'title' => 'Payout Capabilities Enabled',
                'description' => 'Identity and bank payout verification confirmed by Stripe',
                'completed' => $payoutsEnabled,
            ],
        ];

        $completedCount = count(array_filter($checklist, fn ($i) => $i['completed']));
        $isReady = $completedCount === count($checklist);

        $status = 'not_connected';
        if ($hasAccount && $payoutsEnabled) {
            $status = 'payouts_enabled';
        } elseif ($hasAccount && !$payoutsEnabled) {
            $status = 'pending_verification';
        }

        return [
            'connected' => $hasAccount,
            'status' => $status,
            'account_id' => $accountId,
            'masked_account_id' => $maskedId,
            'payouts_enabled' => $payoutsEnabled,
            'currency' => $wallet?->payout_currency ?? $wallet?->currency ?? 'USD',
            'connected_at' => $wallet?->stripe_connected_at ? $wallet->stripe_connected_at->format('M d, Y') : null,
            'is_ready' => $isReady,
            'checklist' => $checklist,
            'completed_count' => $completedCount,
            'total_count' => count($checklist),
        ];
    }

    /**
     * Mark onboarding complete.
     */
    public static function completeOnboarding(User $user): void
    {
        $wallet = $user->wallet ?? Wallet::create(['user_id' => $user->id]);

        $wallet->update([
            'stripe_onboarding_completed' => true,
            'payouts_enabled' => true,
            'stripe_connected_at' => now(),
        ]);
    }

    /**
     * Disconnect Stripe account.
     */
    public static function disconnectStripe(User $user): void
    {
        if ($user->wallet) {
            $user->wallet->update([
                'stripe_account_id' => null,
                'stripe_onboarding_completed' => false,
                'payouts_enabled' => false,
                'stripe_connected_at' => null,
            ]);
        }
    }

    /**
     * Handle Stripe Webhooks for account update events.
     */
    public static function handleWebhook(array $payload): bool
    {
        $type = $payload['type'] ?? '';

        if ($type === 'account.updated') {
            $account = $payload['data']['object'] ?? [];
            $accountId = $account['id'] ?? null;

            if ($accountId) {
                $wallet = Wallet::where('stripe_account_id', $accountId)->first();
                if ($wallet) {
                    $payoutsEnabled = (bool) ($account['payouts_enabled'] ?? false);
                    $detailsSubmitted = (bool) ($account['details_submitted'] ?? false);

                    $wallet->update([
                        'payouts_enabled' => $payoutsEnabled,
                        'stripe_onboarding_completed' => $detailsSubmitted,
                    ]);

                    return true;
                }
            }
        }

        return false;
    }
}
