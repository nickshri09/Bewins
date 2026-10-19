<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('custom_services', function (Blueprint $table) {
            // Drop old foreign key if exists
            if (Schema::hasColumn('custom_services', 'service_id')) {
                try {
                    $table->dropForeign(['service_id']);
                } catch (\Throwable $e) {
                    // ignore if not exists
                }
            }

            // Add new columns
            if (!Schema::hasColumn('custom_services', 'main_service_id')) {
                $table->unsignedBigInteger('main_service_id')->after('id');
            }
            if (!Schema::hasColumn('custom_services', 'main_service_name')) {
                $table->string('main_service_name')->nullable()->after('main_service_id');
            }

            // This migration may be retried after an earlier attempt added the FK
            // but failed before Laravel recorded the migration as complete.
            $foreignKeyExists = DB::table('information_schema.TABLE_CONSTRAINTS')
                ->where('CONSTRAINT_SCHEMA', DB::raw('DATABASE()'))
                ->where('TABLE_NAME', 'custom_services')
                ->where('CONSTRAINT_NAME', 'custom_services_main_service_id_foreign')
                ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
                ->exists();

            if (!$foreignKeyExists) {
                $table->foreign('main_service_id')->references('id')->on('main_services')->onDelete('cascade');
            }

            // Optionally drop old columns
            if (Schema::hasColumn('custom_services', 'custom_service_name')) {
                $table->dropColumn('custom_service_name');
            }
            if (Schema::hasColumn('custom_services', 'service_id')) {
                $table->dropColumn('service_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_services', function (Blueprint $table) {
            // Drop new FK and columns
            try {
                $table->dropForeign(['main_service_id']);
            } catch (\Throwable $e) {
                // ignore if not exists
            }
            if (Schema::hasColumn('custom_services', 'main_service_id')) {
                $table->dropColumn('main_service_id');
            }
            if (Schema::hasColumn('custom_services', 'main_service_name')) {
                $table->dropColumn('main_service_name');
            }

            // Restore old columns
            if (!Schema::hasColumn('custom_services', 'service_id')) {
                $table->unsignedBigInteger('service_id')->after('id');
                $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            }
            if (!Schema::hasColumn('custom_services', 'custom_service_name')) {
                $table->string('custom_service_name')->nullable()->after('service_id');
            }
        });
    }
};


