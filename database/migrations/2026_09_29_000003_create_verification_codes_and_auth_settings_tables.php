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
        // 1. Add role & status to users table if not already present
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('clipper')->after('email'); // admin, brand, clipper
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active')->after('role'); // active, suspended, pending
            }
        });

        // 2. Verification Codes Table (Secure Hashed OTPs, Purpose Bound, Attempt-Limited)
        Schema::create('verification_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('email')->index();
            $table->string('code_hash'); // SHA-256 / bcrypt hash of OTP
            $table->string('purpose', 50)->default('email_verification')->index(); // email_verification, password_reset, email_change, login_challenge
            $table->timestamp('expires_at')->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->unsignedInteger('sent_count')->default(1);
            $table->timestamp('consumed_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['email', 'purpose']);
        });

        // 3. Auth & Security Settings Table
        Schema::create('auth_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general')->index(); // general, google, email_policy, verification, security, smtp
            $table->boolean('is_secret')->default(false);
            $table->timestamps();
        });

        // 4. Disposable & Custom Email Domains
        Schema::create('disposable_email_domains', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique();
            $table->boolean('is_blocked')->default(true); // true = blocked, false = whitelisted exception
            $table->string('reason')->nullable();
            $table->string('added_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposable_email_domains');
        Schema::dropIfExists('auth_settings');
        Schema::dropIfExists('verification_codes');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
