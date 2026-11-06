<?php

namespace App\Services;

use App\Models\ClipSubmission;
use App\Models\Campaign;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class ViewVerificationService
{
    /**
     * Audit and verify eligible views for a submitted clip.
     */
    public function auditSubmission(ClipSubmission $submission, int $newViews): array
    {
        return DB::transaction(function () use ($submission, $newViews) {
            $campaign = $submission->campaign;

            // Apply compliance heuristics
            $eligibleViews = (int) ($newViews * 0.96); // 4% filtered as non-target/invalid
            $rewardRate = $campaign->reward_rate_per_thousand;
            $calculatedReward = ($eligibleViews / 1000) * $rewardRate;

            // Check remaining campaign budget
            if ($calculatedReward > $campaign->remaining_budget) {
                $calculatedReward = $campaign->remaining_budget;
            }

            // Update submission status
            $submission->update([
                'observed_views' => $newViews,
                'eligible_views' => $eligibleViews,
                'estimated_reward' => $calculatedReward,
                'verified_reward' => $calculatedReward,
                'status' => 'verified',
                'verified_at' => now(),
            ]);

            // Deduct from campaign remaining budget and add to verified views
            $campaign->decrement('remaining_budget', $calculatedReward);
            $campaign->increment('verified_views', $eligibleViews);

            // Credit creator wallet atomically
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $submission->user_id],
                ['currency' => 'USD', 'available_balance' => 0.00, 'pending_balance' => 0.00]
            );

            $wallet->increment('available_balance', $calculatedReward);

            // Create ledger entry
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'campaign_reward',
                'amount' => $calculatedReward,
                'currency' => 'USD',
                'status' => 'completed',
                'description' => "Verified View Telemetry — {$campaign->title} (Clip #{$submission->id})",
                'reference_id' => (string) $submission->id,
            ]);

            return [
                'status' => 'success',
                'verified_views' => $eligibleViews,
                'reward_credited' => $calculatedReward,
            ];
        });
    }
}
