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
        // 1. System Releases Table
        Schema::create('system_releases', function (Blueprint $table) {
            $table->id();
            $table->string('version')->index();
            $table->string('release_id')->unique();
            $table->string('type')->default('full'); // full, patch, hotfix
            $table->text('description')->nullable();
            $table->json('manifest')->nullable();
            $table->string('archive_hash', 64)->nullable();
            $table->boolean('signature_verified')->default(false);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('status')->default('STAGED'); // UPLOADED, VALIDATING, BACKUP_CREATED, STAGED, DEPLOYING, MIGRATING, COMPLETED, FAILED, ROLLED_BACK
            $table->string('previous_version')->nullable();
            $table->unsignedBigInteger('backup_id')->nullable();
            $table->string('failure_stage')->nullable();
            $table->text('failure_message')->nullable();
            $table->string('rollback_status')->nullable(); // PENDING, SUCCESS, FAILED, NOT_APPLICABLE
            $table->timestamps();
        });

        // 2. System Update Step Logs
        Schema::create('system_update_steps', function (Blueprint $table) {
            $table->id();
            $table->string('release_id')->index();
            $table->string('step'); // VALIDATE, BACKUP, STAGE, PREFLIGHT, MAINTENANCE, DEPLOY, MIGRATE, OPTIMIZE, HEALTH, ACTIVATE
            $table->string('status')->default('PENDING'); // PENDING, RUNNING, SUCCESS, FAILED, SKIPPED
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('message')->nullable();
            $table->text('safe_error')->nullable();
            $table->timestamps();
        });

        // 3. System Backups Table
        Schema::create('system_backups', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('pre_update'); // pre_update, manual, scheduled
            $table->string('version')->nullable();
            $table->string('release_id')->nullable()->index();
            $table->string('storage_path');
            $table->unsignedBigInteger('size')->default(0);
            $table->string('checksum', 64)->nullable();
            $table->string('status')->default('VERIFIED'); // CREATING, VERIFIED, CORRUPTED, RESTORED
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        // 4. Installed Plugins Table
        Schema::create('installed_plugins', function (Blueprint $table) {
            $table->id();
            $table->string('plugin_id')->unique();
            $table->string('name');
            $table->string('version');
            $table->string('provider')->nullable();
            $table->string('author')->nullable();
            $table->string('type')->default('filament'); // filament, core, third_party
            $table->string('status')->default('INSTALLED'); // INSTALLED, ENABLED, DISABLED, FAILED
            $table->string('source')->default('bundle_upload');
            $table->json('manifest')->nullable();
            $table->json('panels')->nullable(); // ["admin", "brand", "clipper"]
            $table->foreignId('installed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('installed_at')->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->timestamps();
        });

        // 5. Plugin Migrations History Table
        Schema::create('plugin_migrations', function (Blueprint $table) {
            $table->id();
            $table->string('plugin_id')->index();
            $table->string('migration');
            $table->string('version')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plugin_migrations');
        Schema::dropIfExists('installed_plugins');
        Schema::dropIfExists('system_backups');
        Schema::dropIfExists('system_update_steps');
        Schema::dropIfExists('system_releases');
    }
};
