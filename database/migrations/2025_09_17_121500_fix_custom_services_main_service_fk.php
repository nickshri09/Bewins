<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Discover actual constraints instead of attempting to drop guessed
        // constraint names (MySQL errors when any guessed name is absent).
        $foreignKeys = DB::select(
            "SELECT DISTINCT CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL
               AND COLUMN_NAME IN ('main_service_id', 'service_id')",
            ['custom_services']
        );

        foreach ($foreignKeys as $foreignKey) {
            Schema::table('custom_services', function (Blueprint $table) use ($foreignKey) {
                $table->dropForeign($foreignKey->CONSTRAINT_NAME);
            });
        }

        if (Schema::hasColumn('custom_services', 'main_service_id')
            && Schema::hasTable('main_services')) {
            Schema::table('custom_services', function (Blueprint $table) {
                $table->foreign('main_service_id')
                    ->references('id')
                    ->on('main_services')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::table('custom_services', function (Blueprint $table) {
            try { $table->dropForeign(['main_service_id']); } catch (\Throwable $e) {}
            // Optionally restore old FK to services if the column still exists
            if (Schema::hasColumn('custom_services', 'service_id')) {
                $table->foreign('service_id')
                    ->references('id')
                    ->on('services')
                    ->onDelete('cascade');
            }
        });
    }
};


