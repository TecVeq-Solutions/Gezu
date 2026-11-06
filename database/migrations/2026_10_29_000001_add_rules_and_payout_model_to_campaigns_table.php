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
        if (Schema::hasTable('campaigns')) {
            Schema::table('campaigns', function (Blueprint $table) {
                if (!Schema::hasColumn('campaigns', 'rules')) {
                    $table->text('rules')->nullable()->after('description');
                }
                if (!Schema::hasColumn('campaigns', 'payout_model')) {
                    $table->string('payout_model', 50)->default('cpm')->after('category');
                }
                if (!Schema::hasColumn('campaigns', 'reward_per_video')) {
                    $table->decimal('reward_per_video', 10, 2)->nullable()->after('reward_rate_per_thousand');
                }
                if (!Schema::hasColumn('campaigns', 'target_views_per_video')) {
                    $table->unsignedBigInteger('target_views_per_video')->nullable()->after('reward_per_video');
                }
                if (!Schema::hasColumn('campaigns', 'asset_url')) {
                    $table->string('asset_url', 255)->nullable()->after('source_video_url');
                }
                if (!Schema::hasColumn('campaigns', 'allowed_platforms')) {
                    $table->longText('allowed_platforms')->nullable()->after('requirements_json');
                }
                if (!Schema::hasColumn('campaigns', 'expires_at')) {
                    $table->date('expires_at')->nullable()->after('end_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('campaigns')) {
            Schema::table('campaigns', function (Blueprint $table) {
                if (Schema::hasColumn('campaigns', 'rules')) {
                    $table->dropColumn('rules');
                }
                if (Schema::hasColumn('campaigns', 'payout_model')) {
                    $table->dropColumn('payout_model');
                }
                if (Schema::hasColumn('campaigns', 'reward_per_video')) {
                    $table->dropColumn('reward_per_video');
                }
                if (Schema::hasColumn('campaigns', 'target_views_per_video')) {
                    $table->dropColumn('target_views_per_video');
                }
                if (Schema::hasColumn('campaigns', 'asset_url')) {
                    $table->dropColumn('asset_url');
                }
                if (Schema::hasColumn('campaigns', 'allowed_platforms')) {
                    $table->dropColumn('allowed_platforms');
                }
                if (Schema::hasColumn('campaigns', 'expires_at')) {
                    $table->dropColumn('expires_at');
                }
            });
        }
    }
};
