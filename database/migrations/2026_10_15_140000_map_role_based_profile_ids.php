<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'user_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('user_id', 20)->nullable();
            });
        }

        DB::table('users')->where('user_id', '')->update(['user_id' => null]);

        $roles = DB::table('roles')->pluck('name', 'id');
        $users = DB::table('users')
            ->where('user_type', 'user')
            ->orderBy('id')
            ->get(['id', 'role_id']);

        $usedIds = DB::table('users')
            ->where('user_type', '!=', 'user')
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->flip();
        $sequences = [];

        foreach ($users as $user) {
            $roleName = strtolower((string) ($roles[$user->role_id] ?? ''));
            if (str_contains($roleName, 'dsa')) {
                $prefix = 'DSA';
            } elseif (str_contains($roleName, 'employee') || str_contains($roleName, 'manager')) {
                $prefix = 'EMP';
            } else {
                $prefix = 'BUS';
            }

            do {
                $sequences[$prefix] = ($sequences[$prefix] ?? 0) + 1;
                $profileId = $prefix . '-' . str_pad((string) $sequences[$prefix], 4, '0', STR_PAD_LEFT);
            } while (isset($usedIds[$profileId]));

            DB::table('users')->where('id', $user->id)->update(['user_id' => $profileId]);
            $usedIds[$profileId] = true;
        }

        $hasUniqueIndex = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::raw('DATABASE()'))
            ->where('TABLE_NAME', 'users')
            ->where('COLUMN_NAME', 'user_id')
            ->where('NON_UNIQUE', 0)
            ->exists();

        if (!$hasUniqueIndex) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('user_id', 'users_user_id_unique');
            });
        }
    }

    public function down(): void
    {
        // Keep assigned profile IDs and the legacy user_id column on rollback.
    }
};
