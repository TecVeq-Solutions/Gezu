<?php

namespace App\Services;

use App\Models\Payout;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Collection;

class ClipperFinanceService
{
    /**
     * Compute authentic financial summary for a clipper.
     */
    public static function getFinancialSummary(User $user): array
    {
        $wallet = $user->wallet;
        $profile = $user->creatorProfile;

        $paidOut = (float) Payout::where('clipper_id', $user->id)
            ->where('status', 'PAID')
            ->sum('net_payout');

        $pendingEarnings = (float) Payout::where('clipper_id', $user->id)
            ->whereIn('status', ['PENDING_ADMIN_REVIEW', 'APPROVED_FOR_PAYOUT', 'PROCESSING'])
            ->sum('net_payout') + (float) ($wallet?->pending_balance ?? 0.00);

        $availableBalance = (float) ($wallet?->available_balance ?? 0.00);
        $totalWithdrawn = (float) ($wallet?->total_withdrawn ?? 0.00);

        $lifetimeEarnings = $paidOut + $availableBalance + $totalWithdrawn + (float) ($profile?->total_earnings ?? 0.00);

        return [
            'lifetime_earnings' => $lifetimeEarnings,
            'available_balance' => $availableBalance,
            'pending_earnings' => $pendingEarnings,
            'paid_out' => $paidOut > 0 ? $paidOut : $totalWithdrawn,
            'currency' => $wallet?->currency ?? 'USD',
        ];
    }

    /**
     * Get payouts and eligible earnings breakdown.
     */
    public static function getEarningsBreakdown(User $user, string $tab = 'all'): Collection
    {
        $query = Payout::query()
            ->with(['campaign.brand', 'submission'])
            ->where('clipper_id', $user->id);

        if ($tab === 'pending') {
            $query->whereIn('status', ['PENDING_ADMIN_REVIEW', 'APPROVED_FOR_PAYOUT', 'PROCESSING']);
        } elseif ($tab === 'paid') {
            $query->where('status', 'PAID');
        } elseif ($tab === 'available') {
            $query->where('status', 'APPROVED_FOR_PAYOUT');
        }

        return $query->latest()->get();
    }

    /**
     * Get wallet transactions ledger.
     */
    public static function getWalletTransactions(User $user): Collection
    {
        if (!$user->wallet) {
            return new Collection();
        }

        return WalletTransaction::query()
            ->where('wallet_id', $user->wallet->id)
            ->latest()
            ->get();
    }
}
