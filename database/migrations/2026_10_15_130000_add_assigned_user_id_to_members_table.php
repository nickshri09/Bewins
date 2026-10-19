<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('members', 'assigned_user_id')) {
            Schema::table('members', function (Blueprint $table) {
                $table->unsignedBigInteger('assigned_user_id')->nullable()->index();
            });
        }

        // Preserve the current owner mapping from older installations.
        if (Schema::hasColumn('members', 'created_by')) {
            DB::table('members')
                ->whereNull('assigned_user_id')
                ->update(['assigned_user_id' => DB::raw('created_by')]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('members', 'assigned_user_id')) {
            Schema::table('members', function (Blueprint $table) {
                $table->dropColumn('assigned_user_id');
            });
        }
    }
};
