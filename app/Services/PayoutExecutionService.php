<?php

namespace App\Services;

use App\Enums\NotificationEvent;
use App\Models\Payout;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PayoutExecutionService
{
    public function __construct(
        protected StripePayoutService $stripeService,
        protected NotificationService $notificationService
    ) {}

    /**
     * Execute manual admin payout with pessimistic locking and double-spend immunity.
     */
    public function executeManualPayout(Payout $payout, User $adminUser, ?string $customDestination = null): Payout
    {
        $idempotencyKey = 'gezux-payout-' . $payout->id . '-' . $payout->clipper_id;

        // 1. Execute inside atomic database transaction with pessimistic locking
        $processedPayout = DB::transaction(function () use ($payout, $adminUser, $customDestination, $idempotencyKey) {
            /** @var Payout $lockedPayout */
            $lockedPayout = Payout::where('id', $payout->id)->lockForUpdate()->firstOrFail();

            // Guard against duplicate execution
            if ($lockedPayout->status === 'PAID') {
                throw new Exception('Payout has already been executed and marked as PAID.');
            }

            if ($lockedPayout->status === 'PROCESSING') {
                throw new Exception('Payout is currently being processed by another administrator.');
            }

            // Mark as PROCESSING
            $lockedPayout->update(['status' => 'PROCESSING']);

            // Recalculate amounts server-side (Zero trust for frontend inputs)
            $gross = (float) $lockedPayout->gross_earning;
            $commissionRate = (float) ($lockedPayout->commission_rate > 0 ? $lockedPayout->commission_rate : 10.00);
            $commissionAmount = round($gross * ($commissionRate / 100), 2);
            $netPayout = max(0.00, $gross - $commissionAmount);
            $destination = $customDestination ?? $lockedPayout->destination_account ?? 'acct_stripe_connected';

            $lockedPayout->update([
                'commission_rate' => $commissionRate,
                'commission_amount' => $commissionAmount,
                'net_payout' => $netPayout,
                'destination_account' => $destination,
                'idempotency_key' => $idempotencyKey,
            ]);

            try {
                // Call Stripe / Provider Transfer
                $transferResult = $this->stripeService->executePayout($lockedPayout, $destination);
                $providerRef = $transferResult['transfer_id'] ?? ('po_gezux_' . strtoupper(uniqid()));

                // Update Payout Record to PAID
                $lockedPayout->update([
                    'status' => 'PAID',
                    'provider_reference' => $providerRef,
                    'admin_reviewer_id' => $adminUser->id,
                    'reviewed_by_admin_at' => now(),
                    'paid_at' => now(),
                    'failure_reason' => null,
                ]);

                // Update Clipper Wallet & Append to Immutable Ledger
                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $lockedPayout->clipper_id],
                    ['currency' => $lockedPayout->currency ?? 'USD', 'available_balance' => 0.00]
                );

                $wallet->increment('total_withdrawn', $netPayout);

                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'withdrawal',
                    'amount' => -$netPayout,
                    'currency' => $lockedPayout->currency ?? 'USD',
                    'status' => 'completed',
                    'description' => "Manual Payout for submission #{$lockedPayout->submission_id} (Ref: {$providerRef})",
                    'reference_id' => $providerRef,
                ]);

                return $lockedPayout;
            } catch (\Throwable $e) {
                // Handle failure cleanly without corrupting financial state
                $lockedPayout->update([
                    'status' => 'FAILED',
                    'failure_reason' => $e->getMessage(),
                    'admin_reviewer_id' => $adminUser->id,
                    'reviewed_by_admin_at' => now(),
                ]);

                throw $e;
            }
        });

        // 2. Dispatch Multi-Channel Notifications to Clipper
        $clipper = $processedPayout->clipper;
        $this->notificationService->send(
            user: $clipper,
            event: NotificationEvent::PAYOUT_COMPLETED,
            title: 'GEZUX Payout Sent!',
            body: "Your payout of \${$processedPayout->net_payout} for submission #{$processedPayout->submission_id} has been processed successfully.",
            actionUrl: '/dashboard/earnings',
            metadata: [
                'payout_id' => $processedPayout->id,
                'gross_earning' => $processedPayout->gross_earning,
                'commission_deducted' => $processedPayout->commission_amount,
                'net_payout' => $processedPayout->net_payout,
                'provider_reference' => $processedPayout->provider_reference,
                'paid_at' => $processedPayout->paid_at?->toDateTimeString(),
            ]
        );

        return $processedPayout;
    }
}
