<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupOrphanInvestments extends Command
{
    protected $signature = 'investments:cleanup-orphans {--force-delete : Actually delete orphan records}';

    protected $description = 'Find and optionally delete investments whose customer_id does not exist in members';

    public function handle(): int
    {
        $orphanIds = DB::table('investments as i')
            ->leftJoin('members as m', 'm.id', '=', 'i.customer_id')
            ->whereNull('m.id')
            ->orderBy('i.id')
            ->pluck('i.id')
            ->all();

        if (empty($orphanIds)) {
            $this->info('No orphan investments found.');
            return self::SUCCESS;
        }

        $this->warn('Orphan investments found: ' . implode(', ', $orphanIds));

        if (!$this->option('force-delete')) {
            $this->line('Re-run with --force-delete to remove these records.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($orphanIds) {
            DB::table('investment_repayments')->whereIn('investment_id', $orphanIds)->delete();
            DB::table('investments')->whereIn('id', $orphanIds)->delete();
        });

        $this->info('Orphan investments and related repayments deleted successfully.');

        return self::SUCCESS;
    }
}
