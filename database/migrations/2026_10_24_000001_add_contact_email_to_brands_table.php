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
        if (Schema::hasTable('brands')) {
            Schema::table('brands', function (Blueprint $table) {
                if (!Schema::hasColumn('brands', 'contact_email')) {
                    $table->string('contact_email', 150)->nullable()->after('company_name');
                }
                if (!Schema::hasColumn('brands', 'status')) {
                    $table->string('status', 50)->default('active')->after('verification_status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('brands')) {
            Schema::table('brands', function (Blueprint $table) {
                if (Schema::hasColumn('brands', 'contact_email')) {
                    $table->dropColumn('contact_email');
                }
                if (Schema::hasColumn('brands', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }
    }
};
