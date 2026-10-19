<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('members', 'referral_code')) {
            Schema::table('members', function (Blueprint $table) {
                $table->string('referral_code', 20)->nullable()->unique();
            });
        }

        $lastNumber = 0;
        foreach (DB::table('members')->whereNotNull('referral_code')->pluck('referral_code') as $code) {
            if (preg_match('/^RB(\d+)$/', (string) $code, $matches)) {
                $lastNumber = max($lastNumber, (int) $matches[1]);
            }
        }

        $membersWithoutCode = DB::table('members')
            ->whereNull('referral_code')
            ->orWhere('referral_code', '')
            ->orderBy('id')
            ->get(['id']);

        foreach ($membersWithoutCode as $member) {
            $lastNumber++;
            DB::table('members')->where('id', $member->id)->update([
                'referral_code' => 'RB' . str_pad((string) $lastNumber, 4, '0', STR_PAD_LEFT),
            ]);
        }

        DB::table('serial_counters')->updateOrInsert(
            ['prefix' => 'RB'],
            [
                'last_number' => $lastNumber,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        if (Schema::hasColumn('members', 'referral_code')) {
            Schema::table('members', function (Blueprint $table) {
                $table->dropUnique(['referral_code']);
                $table->dropColumn('referral_code');
            });
        }

        DB::table('serial_counters')->where('prefix', 'RB')->delete();
    }
};
