<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            if (!Schema::hasColumn('wallets', 'payout_bank_name')) {
                $table->string('payout_bank_name')->nullable()->after('currency');
            }
            if (!Schema::hasColumn('wallets', 'payout_account_holder')) {
                $table->string('payout_account_holder')->nullable()->after('payout_bank_name');
            }
            if (!Schema::hasColumn('wallets', 'payout_account_number')) {
                $table->string('payout_account_number')->nullable()->after('payout_account_holder');
            }
            if (!Schema::hasColumn('wallets', 'payout_phone_number')) {
                $table->string('payout_phone_number')->nullable()->after('payout_account_number');
            }
            if (!Schema::hasColumn('wallets', 'payout_routing_swift')) {
                $table->string('payout_routing_swift')->nullable()->after('payout_phone_number');
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
                'payout_bank_name',
                'payout_account_holder',
                'payout_account_number',
                'payout_phone_number',
                'payout_routing_swift',
            ]);
        });
    }
};
