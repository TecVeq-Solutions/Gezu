<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Campaign Participations and Saved Campaigns.
     */
    public function up(): void
    {
        if (!Schema::hasTable('campaign_participations')) {
            Schema::create('campaign_participations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('status')->default('joined'); // joined, in_progress, submitted, completed, left
                $table->timestamp('joined_at')->useCurrent();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->unique(['campaign_id', 'user_id']);
                $table->index(['user_id', 'status']);
            });
        }

        if (!Schema::hasTable('saved_campaigns')) {
            Schema::create('saved_campaigns', function (Blueprint $table) {
                $table->id();
                $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['campaign_id', 'user_id']);
                $table->index('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_campaigns');
        Schema::dropIfExists('campaign_participations');
    }
};
