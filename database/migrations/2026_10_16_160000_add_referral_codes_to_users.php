<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'referral_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('referral_code', 20)->nullable()->unique()->after('user_id');
            });
        }

        $lastNumber = 0;
        foreach (DB::table('users')->whereNotNull('referral_code')->pluck('referral_code') as $code) {
            if (preg_match('/^RBU(\d+)$/', (string) $code, $matches)) {
                $lastNumber = max($lastNumber, (int) $matches[1]);
            }
        }

        $usersWithoutCode = DB::table('users')
            ->where('user_type', '!=', 'admin')
            ->where(function ($query) {
                $query->whereNull('referral_code')->orWhere('referral_code', '');
            })
            ->orderBy('id')
            ->get(['id']);

        foreach ($usersWithoutCode as $user) {
            $lastNumber++;
            DB::table('users')->where('id', $user->id)->update([
                'referral_code' => 'RBU' . str_pad((string) $lastNumber, 4, '0', STR_PAD_LEFT),
            ]);
        }

        DB::table('serial_counters')->updateOrInsert(
            ['prefix' => 'RBU'],
            ['last_number' => $lastNumber, 'updated_at' => now(), 'created_at' => now()]
        );
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'referral_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['referral_code']);
                $table->dropColumn('referral_code');
            });
        }

        DB::table('serial_counters')->where('prefix', 'RBU')->delete();
    }
};
