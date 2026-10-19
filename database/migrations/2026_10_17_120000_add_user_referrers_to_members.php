<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('members', 'referred_by_user_id')) {
            Schema::table('members', function (Blueprint $table) {
                $table->foreignId('referred_by_user_id')->nullable()->after('referred_by_member_id')
                    ->constrained('users')->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('users', 'referral_code') || !Schema::hasTable('serial_counters')) {
            return;
        }

        $lastNumber = 0;
        foreach (DB::table('users')->whereNotNull('referral_code')->pluck('referral_code') as $code) {
            if (preg_match('/^RBU(\d+)$/', (string) $code, $matches)) {
                $lastNumber = max($lastNumber, (int) $matches[1]);
            }
        }

        $lastNumber = max($lastNumber, (int) DB::table('serial_counters')->where('prefix', 'RBU')->value('last_number'));
        $usersWithoutCode = DB::table('users')
            ->where('user_type', 'user')
            ->where(function ($query) {
                $query->whereNull('referral_code')->orWhere('referral_code', '');
            })
            ->orderBy('id')
            ->get(['id']);

        foreach ($usersWithoutCode as $user) {
            do {
                $lastNumber++;
                $code = 'RBU' . str_pad((string) $lastNumber, 4, '0', STR_PAD_LEFT);
            } while (DB::table('users')->where('referral_code', $code)->exists());

            DB::table('users')->where('id', $user->id)->update(['referral_code' => $code]);
        }

        DB::table('serial_counters')->updateOrInsert(
            ['prefix' => 'RBU'],
            ['last_number' => $lastNumber, 'updated_at' => now(), 'created_at' => now()]
        );
    }

    public function down(): void
    {
        if (Schema::hasColumn('members', 'referred_by_user_id')) {
            Schema::table('members', function (Blueprint $table) {
                $table->dropForeign(['referred_by_user_id']);
                $table->dropColumn('referred_by_user_id');
            });
        }
    }
};
