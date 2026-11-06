<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Payouts Pipeline.
     */
    public function up(): void
    {
        if (!Schema::hasTable('payouts')) {
            Schema::create('payouts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('submission_id')->constrained('clip_submissions')->cascadeOnDelete();
                $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
                $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
                $table->foreignId('clipper_id')->constrained('users')->cascadeOnDelete();
                
                // Financial Fields (Decimal for absolute monetary precision)
                $table->decimal('gross_earning', 12, 2);
                $table->decimal('commission_rate', 5, 2)->default(10.00); // 10% default GEZUX commission
                $table->decimal('commission_amount', 12, 2)->default(0.00);
                $table->decimal('other_deductions', 12, 2)->default(0.00);
                $table->decimal('net_payout', 12, 2);
                $table->string('currency', 3)->default('USD');
                
                // Payout Lifecycle States
                // PENDING_ADMIN_REVIEW, APPROVED_FOR_PAYOUT, PROCESSING, PAID, FAILED, CANCELLED, ON_HOLD
                $table->string('status')->default('PENDING_ADMIN_REVIEW');
                
                // Destination & Provider Details
                $table->string('payment_method')->default('stripe_connect'); // stripe_connect, wise, crypto, bank_transfer
                $table->string('destination_account')->nullable(); // e.g. acct_123456789
                $table->string('provider_reference')->nullable(); // Stripe transfer / payout ID
                $table->string('idempotency_key')->unique()->nullable();
                $table->text('failure_reason')->nullable();
                
                // Audit & Review Tracking
                $table->foreignId('admin_reviewer_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('approved_by_brand_at')->nullable();
                $table->timestamp('reviewed_by_admin_at')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();

                $table->index(['status', 'clipper_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
