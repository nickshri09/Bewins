<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientInvestmentController extends Controller
{
    private function generateRepaymentSchedule(Investment $investment): void
    {
        if (!$investment->maturity_date) {
            return;
        }

        $existingScheduleCount = DB::table('investment_repayments')
            ->where('investment_id', $investment->id)
            ->count();

        if ($existingScheduleCount > 0) {
            return;
        }

        $startDate = Carbon::parse($investment->start_date);
        $maturityDate = Carbon::parse($investment->maturity_date);
        $principal = (float) $investment->investment_amount;
        $rate = (float) $investment->expected_return;
        $durationYears = $startDate->diffInMonths($maturityDate) / 12;
        $maturityAmount = round($principal + ($principal * $rate * $durationYears / 100), 2);

        if ($investment->contribution_type === 'monthly') {
            $months = max(1, $startDate->diffInMonths($maturityDate));
            $monthlyInterest = ($principal * $rate / 100) / 12;
            $monthlyPrincipal = $principal / $months;
            $totalPrincipalScheduled = 0;

            for ($i = 1; $i <= $months; $i++) {
                $paymentDate = $startDate->copy()->addMonths($i);

                if ($i === $months) {
                    $scheduledPrincipal = round($principal - $totalPrincipalScheduled, 2);
                } else {
                    $scheduledPrincipal = round($monthlyPrincipal, 2);
                }

                $scheduledProfit = round($monthlyInterest, 2);
                $totalPrincipalScheduled += $scheduledPrincipal;

                DB::table('investment_repayments')->insert([
                    'investment_id' => $investment->id,
                    'month_no' => $i,
                    'payment_date' => $paymentDate,
                    'principal' => $scheduledPrincipal,
                    'profit' => $scheduledProfit,
                    'total_amount' => $scheduledPrincipal + $scheduledProfit,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return;
        }

        DB::table('investment_repayments')->insert([
            'investment_id' => $investment->id,
            'month_no' => 1,
            'payment_date' => $maturityDate,
            'principal' => $principal,
            'profit' => round($maturityAmount - $principal, 2),
            'total_amount' => $maturityAmount,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function getClientFromToken(Request $request)
       {
           $token = $request->bearerToken();
           $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

           if (!$accessToken || !$accessToken->tokenable_type) {
               return null;
           }

           return  $accessToken->tokenable_type::withoutGlobalScopes()
            ->find($accessToken->tokenable_id)->load('branch');

        //    return $accessToken->tokenable_type;
       }

       public function store(Request $request)
       {
        //dd($request->all());
        $client = $this->getClientFromToken($request);
        //dd($client);

        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }
          //dd($request->all());
           $request->validate([
               'investment_amount' => 'required|numeric|min:1000',
               'investment_type' => 'required|string',
               'contribution_type' => 'required|in:onetime,monthly',
               'risk_level' => 'required|in:low,medium,high',
               'expected_return'=>'nullable|numeric',
               'start_date' => 'required|date',
               'maturity_date' => 'nullable|date|after_or_equal:start_date',
               'currency_id' => 'required|exists:currency,id',
               'description' => 'nullable|string|max:255',
               'remark' => 'nullable|string|max:255',
           ]);

        //   dd($validated);

        $principal = $request->investment_amount;
        $rate = $request->expected_return;

           $startDate = Carbon::parse($request->start_date);
           $maturityDate = null;
           //  If investment_type = mutual_fund → use user-provided maturity_date
           if ($request->investment_type === 'mutual_fund') {
               $maturityDate = $request->maturity_date
                   ? Carbon::parse($request->maturity_date)
                   : null; // optional
           }

           //  Else (investment, Bonds etc.) → calculate from duration
           else {
               if ($request->duration) {
                   $maturityDate = $startDate->copy()->addMonths($request->duration);
               }
           }

           $durationYears = $request->duration
        ? $request->duration / 12
        : ($maturityDate ? $startDate->diffInMonths($maturityDate) / 12 : 0);

    //  Maturity Amount Calculation (like FD total_paid)
    if ($request->contribution_type == 'monthly') {

        // Compound interest (Monthly)
        $maturityAmount = $principal * pow((1 + ($rate / 100) / 12), 12 * $durationYears);

    } else {

        // Simple interest (One-time)
        $maturityAmount = $principal + ($principal * $rate * $durationYears / 100);

    }



          // dd($request->all(),  $startDate , $maturityDate,$client->id);
        // dd($client->id);
          $investment= Investment::create([
            // dd('ok'),
            //dd($request->remark),
            // dd($request->expected_return),
               'customer_id' => $client->id,
               'investment_amount' => $request->investment_amount,
               'investment_type' => $request->investment_type,
               'contribution_type' => $request->contribution_type,
               'risk_level' => $request->risk_level,
               'expected_return' => $request->expected_return,
               'start_date' => $startDate,
               'maturity_date' => $maturityDate,
            //    'duration' => $request->duration,
               'currency_id' => $request->currency_id,
               'description' => $request->description,
               'remarks' => $request->remark,
                'status' => 0,
                 'maturity_amount' => round($maturityAmount, 2),
           ]);
        $this->generateRepaymentSchedule($investment);

           return response()->json([
            'status'  => 'success',
            'message' => 'Investment created successfully.',
            'data'    => $investment
        ]);
       }


    //     public function AllInvest(Request $request)
    //     {
    //         $client = $this->getClientFromToken($request);
    //         //dd($client);

    //         if (!$client) {
    //             return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
    //         }

    //         //dd($client);
    //         $invest = Investment::with('currency')
    //         ->where('customer_id', $client->id)
    //         ->orderByDesc('id')
    //         ->get();
    //        //dd($invest);
    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'All investment list fetched successfully.',
    //          "data"=>$invest,
    //     ]);

    //  }

    // new
//     public function all_invest(Request $request)
// {
//     $client = $this->getClientFromToken($request);

//     if (!$client) {
//         return response()->json([
//             'status' => 'error',
//             'message' => 'Unauthenticated'
//         ], 401);
//     }

//     // Fetch investments
//     $investments = Investment::with('currency')
//         ->where('customer_id', $client->id)
//         ->orderByDesc('id')
//         ->get();

//     // Add profit & current_value dynamically
//     $data = $investments->map(function ($inv) {
//         $profit = ($inv->investment_amount * $inv->expected_return) / 100;
//         $currentValue = $inv->investment_amount + $profit;

//         return [
//             "id" => $inv->id,
//             "customer_id" => $inv->customer_id,
//             "investment_amount" => $inv->investment_amount,
//             "investment_type" => $inv->investment_type,
//             "contribution_type" => $inv->contribution_type,
//             "risk_level" => $inv->risk_level,
//             "expected_return" => $inv->expected_return,
//             "start_date" => $inv->start_date,
//             "maturity_date" => $inv->maturity_date,
//             "currency" => $inv->currency,
//             "description" => $inv->description,
//             "remarks" => $inv->remarks,
//             "status" => $inv->status,
//             "created_at" => $inv->created_at,
//             "updated_at" => $inv->updated_at,

//             // Calculated fields
//             "profit" => $profit,
//             "current_value" => $currentValue,
//         ];
//     });

//     return response()->json([
//         'status' => 'success',
//         'message' => 'All investment list fetched successfully.',
//         'data' => $data
//     ]);
// }


// new one
public function all_invest(Request $request)
{
    $client = $this->getClientFromToken($request);

    if (!$client) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthenticated'
        ], 401);
    }

    // Fetch all investments
    $investments = Investment::with('currency')
        ->where('customer_id', $client->id)
        ->orderByDesc('id')
        ->get();

    // Add dynamic calculations
    $data = $investments->map(function ($inv) {

        // ------------------------------------------
        // CASE 1: ONETIME INVESTMENT
        // ------------------------------------------
        if ($inv->contribution_type === 'onetime') {

            $profit = ($inv->investment_amount * $inv->expected_return) / 100;
            $currentValue = $inv->investment_amount + $profit;

            $totalInvest = null;
            $totalInterest = null;
            $grandTotal = null;
        }

        // ------------------------------------------
        // CASE 2: MONTHLY INVESTMENT
        // ------------------------------------------
        else {

            $startDate = Carbon::parse($inv->start_date);
            $endDate   = Carbon::parse($inv->maturity_date);

            $months = $startDate->diffInMonths($endDate);

            $principal = $inv->investment_amount;       // Monthly amount
            $rate = $inv->expected_return / 100;        // Annual rate

            // Total principal paid in all months
            $totalInvest = $principal * $months;

            // Monthly interest
            $monthlyInterest = ($principal * $rate) / 12;

            // Total interest in all months
            $totalInterest = $monthlyInterest * $months;

            // Grand total
            $grandTotal = $totalInvest + $totalInterest;

            // For monthly → No one-time profit/current_value
            $profit = null;
            $currentValue = null;
        }

        return [
            "id" => $inv->id,
            "customer_id" => $inv->customer_id,
            "investment_amount" => $inv->investment_amount,
            "investment_type" => $inv->investment_type,
            "contribution_type" => $inv->contribution_type,
            "risk_level" => $inv->risk_level,
            "expected_return" => $inv->expected_return,
            "start_date" => $inv->start_date,
            "maturity_date" => $inv->maturity_date,
            "currency" => $inv->currency,
            "description" => $inv->description,
            "remarks" => $inv->remarks,
            "status" => $inv->status,
            "created_at" => $inv->created_at,
            "updated_at" => $inv->updated_at,

            // ONETIME ONLY
            "profit" => $profit,
            "current_value" => $currentValue,

            // MONTHLY ONLY
            "total_Invest" => $totalInvest,
            "total_interest" => $totalInterest,
            "grand_total" => $grandTotal,
        ];
    });

    return response()->json([
        'status' => 'success',
        'message' => 'All investment list fetched successfully.',
        'data' => $data
    ]);
}


    //  public function showBy_Id(Request $request,$id)
    // {
    //     $client = $this->getClientFromToken($request);
    //     //dd($client);

    //     if (!$client) {
    //         return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
    //     }
    //     //dd($id);
    //     // $investment = Investment::with(['customer', 'currency'])->findOrFail($id);
    //     $investment = Investment::with(['currency'])->where('customer_id',$client->id)->findOrFail($id);
    //     //dd($investment);
    //     $schedule = [];
    //     $totalPrincipal = 0;
    //     $totalInterest  = 0;
    //     $grandTotal     = 0;

    //     if ($investment->contribution_type == 'monthly') {
    //         $startDate = Carbon::parse($investment->start_date);
    //         $endDate   = Carbon::parse($investment->maturity_date);
    //         $today     = Carbon::today();

    //         $months = $startDate->diffInMonths($endDate);

    //         for ($i = 1; $i <= $months; $i++) {
    //             $paymentDate = $startDate->copy()->addMonths($i);

    //             // Status logic
    //             if ($today->gt($paymentDate)) {
    //                 $status = 'paid';
    //             } elseif ($today->eq($paymentDate)) {
    //                 $status = 'due';
    //             } else {
    //                 $status = 'upcoming';
    //             }

    //             // Monthly calculation
    //             $principal      = $investment->investment_amount;       // Monthly contribution
    //             $annualRate     = $investment->expected_return / 100;   // 10% => 0.1
    //             $monthlyInterest= $principal * $annualRate / 12;       // Monthly interest
    //             $total          = $principal + $monthlyInterest;

    //             // Add to totals
    //             $totalPrincipal += $principal;
    //             $totalInterest  += $monthlyInterest;
    //             $grandTotal     += $total;

    //             //dd($investment);
    //             $schedule[] = [
    //                 'month'            => $i,
    //                 'payment_date'     => $paymentDate->format('Y-m-d'),
    //                 'principal'        => number_format($principal, 2),
    //                 'Expected_Returns' => number_format($investment->expected_return, 2),
    //                 'monthly_Interest' => number_format($monthlyInterest, 2),
    //                 'total'            => number_format($total, 2),
    //                 'status'           => $status,
    //             ];
    //         }
    //     }

    //     return response()->json([
    //         'investment'=>$investment,
    //         'schedule' => $schedule,
    //         'totalprice'=>$totalPrincipal,
    //         'grandTotal'=> $grandTotal,
    //     ]);

    // }


    public function showBy_Id(Request $request, $id)
    {

        $client = $this->getClientFromToken($request);

        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }
       //dd($client);
        $investment = Investment::with(['currency'])
            ->where('customer_id', $client->id)
            ->findOrFail($id);

        //dd($investment);
        // COMMON VARIABLES
        $profit = 0;
        $currentValue = 0;
        $schedule = [];
        $totalInvest = 0;
        $totalInterest = 0;
        $grandTotal = 0;

        // ------------------------------------------------------
        //  ONETIME INVESTMENT LOGIC
        // ------------------------------------------------------
        if ($investment->contribution_type === 'onetime') {

            $profit = ($investment->investment_amount * $investment->expected_return) / 100;
            $currentValue = $investment->investment_amount + $profit;

            // Onetime investment → No schedule needed
        }

       // dd($investment , $investment->contribution_type , $profit,$currentValue);
        // ------------------------------------------------------
        //  MONTHLY INVESTMENT LOGIC
        // ------------------------------------------------------
        if ($investment->contribution_type === 'monthly') {

            $startDate = Carbon::parse($investment->start_date);
            $endDate   = Carbon::parse($investment->maturity_date);
            $today     = Carbon::today();
            $rate = $investment->expected_return / 100;  // Annual % rate
            $intervalCount = max(1, $startDate->diffInMonths($endDate));
            $principal = $investment->investment_amount / $intervalCount;
            $profitPerInterval = ($investment->investment_amount * $rate) / 12;

            for ($i = 1; $i <= $intervalCount; $i++) {
                $paymentDate = $startDate->copy()->addMonths($i);

                if ($paymentDate->gt($endDate)) {
                    $paymentDate = $endDate->copy();
                }

                // Status
                if ($today->gt($paymentDate)) {
                    $status = 'paid';
                } elseif ($today->eq($paymentDate)) {
                    $status = 'due';
                } else {
                    $status = 'upcoming';
                }

                $total = $principal + $profitPerInterval;

                // Totals
                $totalInvest += $principal;
                $totalInterest += $profitPerInterval;
                $grandTotal += $total;

                // Add schedule row
                $schedule[] = [
                    'month'            => $i,
                    'payment_date'     => $paymentDate->format('Y-m-d'),
                    'principal'        => number_format($principal, 2),
                    'expected_return'  => number_format($investment->expected_return, 2),
                    'monthly_interest' => number_format($profitPerInterval, 2),
                    'total'            => number_format($total, 2),
                    'status'           => $status,
                ];
            }

            // Monthly investment → no one-time profit or current value
            $profit = null;
            $currentValue = null;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Investment details fetched successfully.',

            'investment' => $investment,

            // Extra calculated values
            'profit' => $profit,
            'current_value' => $currentValue,
            // Only for monthly
            'schedule'    => $schedule,
            'totalInvest' =>$totalInvest,
            'totalInterest'=>$totalInterest,
            'garndTotal'   =>$grandTotal,


        ]);
    }












}
