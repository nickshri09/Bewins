<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('serial_counters')) {
            Schema::create('serial_counters', function (Blueprint $table) {
                $table->string('prefix', 10)->primary();
                $table->unsignedBigInteger('last_number')->default(0);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('investments') && !Schema::hasColumn('investments', 'investment_code')) {
            Schema::table('investments', function (Blueprint $table) {
                $table->string('investment_code', 20)->nullable()->unique();
            });
        }

        if (Schema::hasTable('taxation_orders') && !Schema::hasColumn('taxation_orders', 'tax_code')) {
            Schema::table('taxation_orders', function (Blueprint $table) {
                $table->string('tax_code', 20)->nullable()->unique();
            });
        }

        $lastNumbers = [
            'BUS' => $this->maxSuffix('users', 'user_id', 'BUS'),
            'DSA' => $this->maxSuffix('users', 'user_id', 'DSA'),
            'EMP' => $this->maxSuffix('users', 'user_id', 'EMP'),
            'BCS' => $this->maxSuffix('investments', 'investment_code', 'BCS'),
            'BLN' => $this->maxSuffix('loans', 'loan_id', 'BLN'),
            'BTX' => $this->maxSuffix('taxation_orders', 'tax_code', 'BTX'),
        ];

        if (Schema::hasTable('investments') && Schema::hasColumn('investments', 'investment_code')) {
            $rows = DB::table('investments')->whereNull('investment_code')->orderBy('id')->get(['id']);
            foreach ($rows as $row) {
                $lastNumbers['BCS']++;
                DB::table('investments')->where('id', $row->id)->update([
                    'investment_code' => 'BCS-' . str_pad((string) $lastNumbers['BCS'], 4, '0', STR_PAD_LEFT),
                ]);
            }
        }

        if (Schema::hasTable('taxation_orders') && Schema::hasColumn('taxation_orders', 'tax_code')) {
            $rows = DB::table('taxation_orders')->whereNull('tax_code')->orderBy('id')->get(['id']);
            foreach ($rows as $row) {
                $lastNumbers['BTX']++;
                DB::table('taxation_orders')->where('id', $row->id)->update([
                    'tax_code' => 'BTX-' . str_pad((string) $lastNumbers['BTX'], 4, '0', STR_PAD_LEFT),
                ]);
            }
        }

        foreach ($lastNumbers as $prefix => $lastNumber) {
            DB::table('serial_counters')->updateOrInsert(
                ['prefix' => $prefix],
                ['last_number' => $lastNumber, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('investments') && Schema::hasColumn('investments', 'investment_code')) {
            Schema::table('investments', function (Blueprint $table) {
                $table->dropUnique(['investment_code']);
                $table->dropColumn('investment_code');
            });
        }

        if (Schema::hasTable('taxation_orders') && Schema::hasColumn('taxation_orders', 'tax_code')) {
            Schema::table('taxation_orders', function (Blueprint $table) {
                $table->dropUnique(['tax_code']);
                $table->dropColumn('tax_code');
            });
        }

        Schema::dropIfExists('serial_counters');
    }

    private function maxSuffix(string $table, string $column, string $prefix): int
    {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
            return 0;
        }

        $max = 0;
        foreach (DB::table($table)->where($column, 'like', $prefix . '-%')->pluck($column) as $value) {
            if (preg_match('/^' . preg_quote($prefix, '/') . '-(\d+)$/', (string) $value, $matches)) {
                $max = max($max, (int) $matches[1]);
            }
        }

        return $max;
    }
};
