<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Stripe Connect connected accounts integration.
     */
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            if (!Schema::hasColumn('wallets', 'stripe_account_id')) {
                $table->string('stripe_account_id')->nullable();
                $table->boolean('stripe_onboarding_completed')->default(false);
                $table->boolean('payouts_enabled')->default(false);
                $table->string('payout_currency', 3)->default('USD');
                $table->timestamp('stripe_connected_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_account_id',
                'stripe_onboarding_completed',
                'payouts_enabled',
                'payout_currency',
                'stripe_connected_at',
            ]);
        });
    }
};
