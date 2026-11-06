<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Models\WalletTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class WalletService
{
    /**
     * Request a withdrawal with double-spend protection.
     */
    public function requestWithdrawal(User $user, float $amount, string $method, string $destination): Withdrawal
    {
        return DB::transaction(function () use ($user, $amount, $method, $destination) {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

            if (!$wallet || $wallet->available_balance < $amount) {
                throw new Exception('Insufficient available balance.');
            }

            // Deduct available balance
            $wallet->decrement('available_balance', $amount);

            // Create withdrawal request
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'currency' => $wallet->currency,
                'method' => $method,
                'destination_reference' => $destination,
                'status' => 'completed', // Auto-processed in demo mode
                'processed_at' => now(),
            ]);

            // Ledger entry
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'withdrawal',
                'amount' => -$amount,
                'currency' => $wallet->currency,
                'status' => 'completed',
                'description' => "Payout via {$method} ({$destination})",
                'reference_id' => (string) $withdrawal->id,
            ]);

            return $withdrawal;
        });
    }
}
