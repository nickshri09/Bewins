<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('user_type', 'customer')
            ->where('referral_code', 'like', 'RBU%')
            ->update(['referral_code' => null]);
    }

    public function down(): void
    {
        // Referral codes cleared from client-login rows are intentionally not restored.
    }
};
