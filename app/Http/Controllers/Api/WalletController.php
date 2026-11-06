<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RequestWithdrawalRequest;
use App\Models\Wallet;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Exception;

class WalletController extends Controller
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Get wallet details & ledger transactions for authenticated user.
     */
    public function show(\Illuminate\Http\Request $request): JsonResponse
    {
        $user = $request->user() ?? auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $wallet = Wallet::with('transactions')->firstOrCreate(
            ['user_id' => $user->id],
            ['currency' => 'USD', 'available_balance' => 0.00, 'pending_balance' => 0.00]
        );

        return response()->json($wallet);
    }

    /**
     * Request payout withdrawal for authenticated user.
     */
    public function withdraw(RequestWithdrawalRequest $request): JsonResponse
    {
        $user = $request->user() ?? auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        try {
            $withdrawal = $this->walletService->requestWithdrawal(
                $user,
                (float) $request->amount,
                $request->method,
                $request->destination_reference
            );

            return response()->json([
                'message' => 'Withdrawal processed successfully.',
                'withdrawal' => $withdrawal,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
