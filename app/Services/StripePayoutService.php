<?php

namespace App\Services;

use App\Models\Payout;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripePayoutService
{
    /**
     * Execute manual payout to clipper via Stripe Connect.
     * Enforces idempotency and server-side validation.
     */
    public function executePayout(Payout $payout, string $destinationAccountId): array
    {
        $secretKey = config('services.stripe.secret') ?? env('STRIPE_SECRET');
        $idempotencyKey = $payout->idempotency_key ?? ('gezux-payout-' . $payout->id . '-' . time());

        // Validate amount
        if ($payout->net_payout <= 0) {
            throw new Exception('Invalid net payout amount.');
        }

        // Amount in cents for Stripe API
        $amountInCents = (int) round($payout->net_payout * 100);
        $currency = strtolower($payout->currency ?? 'usd');

        // If Stripe keys are not configured in local demo environment, simulate successful transfer
        if (!$secretKey) {
            $simulatedRef = 'tr_demo_' . strtoupper(uniqid());
            return [
                'status' => 'simulated_success',
                'transfer_id' => $simulatedRef,
                'idempotency_key' => $idempotencyKey,
                'amount' => $payout->net_payout,
                'currency' => $currency,
                'destination' => $destinationAccountId,
                'message' => 'Stripe API simulated in local development environment.',
            ];
        }

        try {
            // Stripe Transfers API (Transfers funds from platform to connected account)
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $secretKey,
                'Idempotency-Key' => $idempotencyKey,
            ])->asForm()->post('https://api.stripe.com/v1/transfers', [
                'amount' => $amountInCents,
                'currency' => $currency,
                'destination' => $destinationAccountId,
                'description' => "GEZUX Payout #{$payout->id} for submission #{$payout->submission_id}",
                'metadata' => [
                    'payout_id' => (string) $payout->id,
                    'submission_id' => (string) $payout->submission_id,
                    'clipper_id' => (string) $payout->clipper_id,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'status' => 'success',
                    'transfer_id' => $data['id'] ?? ('tr_' . uniqid()),
                    'idempotency_key' => $idempotencyKey,
                    'response' => $data,
                ];
            } else {
                $errorMsg = $response->json('error.message') ?? $response->body();
                Log::error("Stripe transfer failed for payout #{$payout->id}: {$errorMsg}");
                throw new Exception("Stripe Transfer Failed: {$errorMsg}");
            }
        } catch (\Throwable $e) {
            Log::error("Stripe payout exception for payout #{$payout->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check Connected Account Payout Eligibility.
     */
    public function checkAccountStatus(string $accountId): array
    {
        $secretKey = config('services.stripe.secret') ?? env('STRIPE_SECRET');

        if (!$secretKey) {
            return [
                'account_id' => $accountId,
                'status' => 'connected',
                'payouts_enabled' => true,
                'charges_enabled' => true,
                'mode' => 'simulated',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $secretKey,
            ])->get("https://api.stripe.com/v1/accounts/{$accountId}");

            if ($response->successful()) {
                $account = $response->json();
                return [
                    'account_id' => $accountId,
                    'payouts_enabled' => (bool) ($account['payouts_enabled'] ?? false),
                    'charges_enabled' => (bool) ($account['charges_enabled'] ?? false),
                    'details_submitted' => (bool) ($account['details_submitted'] ?? false),
                    'status' => ($account['payouts_enabled'] ?? false) ? 'payouts_enabled' : 'restricted',
                ];
            }

            return [
                'account_id' => $accountId,
                'status' => 'not_found_or_restricted',
                'payouts_enabled' => false,
            ];
        } catch (\Throwable $e) {
            return [
                'account_id' => $accountId,
                'status' => 'error',
                'error' => $e->getMessage(),
                'payouts_enabled' => false,
            ];
        }
    }
}
