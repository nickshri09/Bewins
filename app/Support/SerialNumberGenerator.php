<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use RuntimeException;

class SerialNumberGenerator
{
    /**
     * Allocate the next number for a prefix. The counter row is locked so
     * concurrent record creation cannot receive the same serial.
     */
    public static function next(string $prefix): string
    {
        $counterKey = strtoupper($prefix);

        return DB::transaction(function () use ($counterKey, $prefix) {
            $counter = DB::table('serial_counters')
                ->where('prefix', $counterKey)
                ->lockForUpdate()
                ->first();

            if (!$counter) {
                throw new RuntimeException("Serial counter for {$counterKey} is not configured. Run migrations first.");
            }

            $number = (int) $counter->last_number + 1;

            DB::table('serial_counters')
                ->where('prefix', $counterKey)
                ->update(['last_number' => $number, 'updated_at' => now()]);

            return strtoupper($prefix) . '-' . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
        });
    }
}
