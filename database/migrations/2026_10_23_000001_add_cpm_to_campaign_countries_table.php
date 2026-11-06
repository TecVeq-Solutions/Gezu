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
        if (Schema::hasTable('campaign_countries')) {
            Schema::table('campaign_countries', function (Blueprint $table) {
                if (!Schema::hasColumn('campaign_countries', 'cpm')) {
                    $table->decimal('cpm', 10, 2)->nullable()->after('reward_multiplier');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('campaign_countries')) {
            Schema::table('campaign_countries', function (Blueprint $table) {
                if (Schema::hasColumn('campaign_countries', 'cpm')) {
                    $table->dropColumn('cpm');
                }
            });
        }
    }
};
