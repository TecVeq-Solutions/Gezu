<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\ClipSubmissionController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\StripePaymentController;

Route::prefix('v1')->group(function () {
    // Public Campaigns Discovery
    Route::get('/campaigns', [CampaignController::class, 'index']);
    Route::get('/campaigns/{campaign}', [CampaignController::class, 'show']);

    // Authenticated Creator & Brand Operations
    Route::middleware(['auth'])->group(function () {
        // Campaigns
        Route::post('/campaigns', [CampaignController::class, 'store']);

        // Clip Submissions
        Route::get('/submissions', [ClipSubmissionController::class, 'index']);
        Route::post('/submissions', [ClipSubmissionController::class, 'store']);

        // Wallet & Withdrawals
        Route::get('/wallet', [WalletController::class, 'show']);
        Route::post('/wallet/withdraw', [WalletController::class, 'withdraw']);

        // Stripe Funding
        Route::post('/payments/checkout-session', [StripePaymentController::class, 'createCheckoutSession']);
    });

    // Stripe Webhooks (Validated via payload/signature)
    Route::post('/payments/webhook', [StripePaymentController::class, 'handleWebhook'])->middleware('throttle:60,1');
});
