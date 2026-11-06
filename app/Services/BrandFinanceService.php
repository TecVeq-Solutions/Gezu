<?php

namespace App\Services;

use App\Enums\NotificationEvent;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BrandFinanceService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Fund a specific campaign budget directly with guaranteed pool allocation.
     * Note: Brand funding is strictly separated from clipper payouts.
     */
    public function fundCampaign(
        Brand $brand,
        Campaign $campaign,
        float $amount,
        string $paymentMethod = 'stripe_card',
        ?string $referenceId = null
    ): WalletTransaction {
        if ($campaign->brand_id !== $brand->id) {
            throw new Exception('Unauthorized: Campaign does not belong to your brand.');
        }

        if ($amount < 50.00) {
            throw new Exception('Minimum campaign funding amount is $50.00.');
        }

        return DB::transaction(function () use ($brand, $campaign, $amount, $paymentMethod, $referenceId) {
            $user = $brand->user;

            // 1. Get or create brand wallet
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

            // 2. Increase Campaign Total & Remaining Budget
            $campaign->increment('total_budget', $amount);
            $campaign->increment('remaining_budget', $amount);

            // Reactivate if it was paused
            if ($campaign->status === 'paused') {
                $campaign->update(['status' => 'active']);
            }

            // 3. Record immutable WalletTransaction
            $ref = $referenceId ?? ('tx_fund_' . Str::lower(Str::random(12)));
            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'campaign_funding',
                'amount' => $amount,
                'currency' => $campaign->currency ?? 'USD',
                'status' => 'completed',
                'description' => "Budget Allocation: {$campaign->title}",
                'reference_id' => $ref,
            ]);

            // 4. Dispatch Notification
            $this->notificationService->send(
                user: $user,
                event: NotificationEvent::CAMPAIGN_FUNDED,
                title: 'Campaign Budget Funded Successfully',
                body: "Added $" . number_format($amount, 2) . " to '{$campaign->title}'. Total remaining budget: $" . number_format($campaign->remaining_budget, 2),
                actionUrl: "/brand/campaigns/{$campaign->id}",
                metadata: [
                    'campaign_id' => $campaign->id,
                    'amount' => $amount,
                    'reference_id' => $ref,
                ]
            );

            return $transaction;
        });
    }

    /**
     * Get aggregate financial metrics for the brand.
     */
    public static function getFinanceSummary(Brand $brand): array
    {
        $campaigns = Campaign::where('brand_id', $brand->id)->get();
        $totalBudget = (float) $campaigns->sum('total_budget');
        $remainingBudget = (float) $campaigns->sum('remaining_budget');
        $totalSpent = max(0, $totalBudget - $remainingBudget);

        $wallet = Wallet::where('user_id', $brand->user_id)->first();
        $transactionsQuery = $wallet 
            ? WalletTransaction::where('wallet_id', $wallet->id) 
            : null;

        $totalTransactions = $transactionsQuery ? $transactionsQuery->count() : 0;
        $totalFundedTransactions = $transactionsQuery ? (float) (clone $transactionsQuery)->where('type', 'campaign_funding')->sum('amount') : $totalBudget;

        return [
            'total_funded' => max($totalBudget, $totalFundedTransactions),
            'active_escrow' => $remainingBudget,
            'total_spent' => $totalSpent,
            'transactions_count' => $totalTransactions,
        ];
    }
}
