<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for GEZUX Marketplace.
     */
    public function up(): void
    {
        // 1. Brands
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->string('company_slug')->unique();
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->string('industry')->nullable();
            $table->string('country', 2)->default('US');
            $table->string('verification_status')->default('verified');
            $table->timestamps();
        });

        // 2. Creator Profiles
        Schema::create('creator_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('display_name');
            $table->string('username')->unique();
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->string('country', 2)->default('US');
            $table->string('timezone')->default('UTC');
            $table->string('verification_status')->default('verified');
            $table->unsignedBigInteger('total_verified_views')->default(0);
            $table->unsignedInteger('total_campaigns')->default(0);
            $table->decimal('total_earnings', 12, 2)->default(0.00);
            $table->decimal('rating', 3, 2)->default(5.00);
            $table->timestamps();
        });

        // 3. Campaigns
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('category')->default('Technology & AI');
            $table->string('status')->default('active'); // draft, active, paused, completed, cancelled
            $table->string('content_type')->default('video');
            $table->string('source_video_url')->nullable();
            $table->string('source_duration')->default('00:00:00');
            $table->string('thumbnail')->nullable();
            $table->decimal('total_budget', 12, 2);
            $table->decimal('remaining_budget', 12, 2);
            $table->decimal('reward_rate_per_thousand', 8, 2)->default(1.00);
            $table->string('currency', 3)->default('USD');
            $table->unsignedBigInteger('target_views')->default(1000000);
            $table->unsignedBigInteger('verified_views')->default(0);
            $table->json('requirements_json')->nullable();
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
            $table->timestamps();
        });

        // 4. Campaign Countries
        Schema::create('campaign_countries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->string('country_code', 2);
            $table->string('country_name');
            $table->decimal('reward_multiplier', 4, 2)->default(1.00);
            $table->unsignedBigInteger('target_views')->default(0);
            $table->timestamps();
        });

        // 5. Campaign Platforms
        Schema::create('campaign_platforms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->string('platform'); // TikTok, Instagram, YouTube Shorts, X
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });

        // 6. Social Accounts
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('platform');
            $table->string('external_account_id')->nullable();
            $table->string('username');
            $table->string('handle')->nullable();
            $table->string('display_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('avatar_url')->nullable();
            $table->unsignedBigInteger('followers_count')->default(0);
            $table->boolean('verified')->default(false);
            $table->boolean('is_verified')->default(true);
            $table->string('status')->default('connected');
            $table->timestamp('connected_at')->nullable();
            $table->timestamps();
        });

        // 7. Clips & Submissions
        Schema::create('clip_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('platform');
            $table->string('post_url');
            $table->string('external_post_id')->nullable();
            $table->string('status')->default('under_review'); // pending, under_review, verified, rejected, flagged
            $table->unsignedBigInteger('observed_views')->default(0);
            $table->unsignedBigInteger('eligible_views')->default(0);
            $table->decimal('estimated_reward', 10, 2)->default(0.00);
            $table->decimal('verified_reward', 10, 2)->nullable();
            $table->unsignedTinyInteger('fraud_risk_score')->default(0);
            $table->text('rejection_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        // 8. Wallets & Transactions
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('currency', 3)->default('USD');
            $table->decimal('available_balance', 12, 2)->default(0.00);
            $table->decimal('pending_balance', 12, 2)->default(0.00);
            $table->decimal('locked_balance', 12, 2)->default(0.00);
            $table->decimal('total_withdrawn', 12, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // campaign_reward, withdrawal, campaign_funding, platform_fee
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('status')->default('completed'); // pending, completed, failed
            $table->string('description');
            $table->string('reference_id')->nullable();
            $table->timestamps();
        });

        // 9. Withdrawals
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('method'); // Wise, Bank Transfer, Crypto, PayPal
            $table->string('destination_reference');
            $table->string('status')->default('pending'); // pending, processing, completed, rejected
            $table->timestamp('processed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();
        });

        // 10. Fraud Flags & Audit Logs
        Schema::create('fraud_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->nullable()->constrained('clip_submissions')->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reason');
            $table->string('severity')->default('medium'); // low, medium, high
            $table->string('status')->default('under_review'); // under_review, confirmed, cleared
            $table->json('telemetry_metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fraud_flags');
        Schema::dropIfExists('withdrawals');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('clip_submissions');
        Schema::dropIfExists('social_accounts');
        Schema::dropIfExists('campaign_platforms');
        Schema::dropIfExists('campaign_countries');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('creator_profiles');
        Schema::dropIfExists('brands');
    }
};
