<?php

namespace App\Cronjobs;

use App\Models\Investment;
use App\Models\MemberWallet;
use App\Models\MemberWalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DailyInvestmentInterestPosting
{
    public function __invoke(): void
    {
        @ini_set('max_execution_time', 0);
        @set_time_limit(0);

        $today = Carbon::today();
        Log::info('Cronjob Started: DailyInvestmentInterestPosting at ' . now());

        $repayments = DB::table('investment_repayments')
            ->join('investments', 'investment_repayments.investment_id', '=', 'investments.id')
            ->where('investment_repayments.status', 'paid')
            ->where('investments.status', 1)
            ->select(
                'investment_repayments.id',
                'investment_repayments.investment_id',
                'investment_repayments.principal',
                'investment_repayments.paid_date',
                'investment_repayments.interest_posted_upto',
                'investments.expected_return',
                'investments.tds_percent',
                'investments.customer_id'
            )
            ->get();

        Log::info('DailyInvestmentInterestPosting: Found ' . count($repayments) . ' active paid repayments to process.');

        foreach ($repayments as $repayment) {
            $paidDate = Carbon::parse($repayment->paid_date)->startOfDay();
            $fromDate = $repayment->interest_posted_upto
                ? Carbon::parse($repayment->interest_posted_upto)->addDay()->startOfDay()
                : $paidDate->copy()->addDay();

            if ($fromDate->gt($today)) {
                continue;
            }

            $daysToCredit = $fromDate->diffInDays($today) + 1;
            $dailyInterest = ((float) $repayment->principal * ((float) $repayment->expected_return / 100)) / 365;
            $grossAmount = round($dailyInterest * $daysToCredit, 2);

            // Calculate TDS
            $tdsPercentage = (float) ($repayment->tds_percent ?? 0);
            $tdsAmount = round(($grossAmount * $tdsPercentage) / 100, 2);
            $amount = $grossAmount - $tdsAmount;

            if ($amount <= 0 && $grossAmount <= 0) {
                continue;
            }

            $memberExists = DB::table('members')->where('id', $repayment->customer_id)->exists();
            if (!$memberExists) {
                Log::warning('Skipping investment interest credit because member was not found.', [
                    'investment_id' => $repayment->investment_id,
                    'customer_id' => $repayment->customer_id,
                    'repayment_id' => $repayment->id,
                ]);
                continue;
            }

            $wallet = MemberWallet::firstOrCreate(
                ['member_id' => $repayment->customer_id],
                ['balance' => 0]
            );

            DB::transaction(function () use ($wallet, $repayment, $amount, $grossAmount, $tdsAmount, $daysToCredit, $fromDate, $today) {
                $wallet->increment('balance', $amount);

                MemberWalletTransaction::create([
                    'member_wallet_id' => $wallet->id,
                    'member_id' => $repayment->customer_id,
                    'investment_id' => $repayment->investment_id,
                    'investment_repayment_id' => $repayment->id,
                    'type' => 'credit',
                    'gross_amount' => $grossAmount,
                    'tds_amount' => $tdsAmount,
                    'amount' => $amount,
                    'days_count' => $daysToCredit,
                    'posted_from' => $fromDate->toDateString(),
                    'posted_to' => $today->toDateString(),
                    'note' => 'Daily investment interest credit (TDS Deducted)',
                ]);

                DB::table('investment_repayments')
                    ->where('id', $repayment->id)
                    ->update([
                        'interest_posted_upto' => $today->toDateString(),
                        'updated_at' => now(),
                    ]);

                Log::info("DailyInvestmentInterestPosting: Credited {$amount} to Member ID: {$repayment->customer_id} for Investment ID: {$repayment->investment_id}");
            });
        }

        Log::info('Cronjob Finished: DailyInvestmentInterestPosting at ' . now());
    }
}
