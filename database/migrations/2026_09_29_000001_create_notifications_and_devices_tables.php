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
        // 1. Standard Laravel Notifications Table
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        // 2. User Notification Devices (Firebase FCM Web Push Tokens)
        Schema::create('user_notification_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token', 500)->unique();
            $table->string('device_type')->default('browser'); // desktop_chrome, mobile_safari, etc.
            $table->string('platform')->nullable(); // Windows, macOS, Android, iOS
            $table->text('user_agent')->nullable();
            $table->string('permission_status')->default('granted'); // granted, denied, default
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('last_successful_delivery_at')->nullable();
            $table->timestamp('last_failure_at')->nullable();
            $table->text('last_failure_reason')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'permission_status']);
        });

        // 3. Immutable Multi-Channel Notification Audit Logs
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event'); // e.g. BRAND_APPROVED_SUBMISSION, PAYOUT_REVIEW_REQUIRED
            $table->string('channel'); // database, email, firebase
            $table->string('title');
            $table->text('body');
            $table->string('action_url')->nullable();
            $table->string('status')->default('sent'); // queued, sent, failed
            $table->string('provider_reference')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'event', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('user_notification_devices');
        Schema::dropIfExists('notifications');
    }
};
