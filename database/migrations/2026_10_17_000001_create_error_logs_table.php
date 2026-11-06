<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('error_logs')) {
            Schema::create('error_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('exception_type', 255)->default('Exception');
                $table->text('message');
                $table->string('file', 500)->nullable();
                $table->integer('line')->nullable();
                $table->text('url')->nullable();
                $table->longText('trace')->nullable();
                $table->timestamps();

                $table->index('created_at');
                $table->index('exception_type');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('error_logs');
    }
};
