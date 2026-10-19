<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FD;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientFDController extends Controller
{
       //  Helper to get authenticated client
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

        // dd($request->all());
        $client = $this->getClientFromToken($request);
        //dd($client);

        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'currency_id' => 'required|exists:currency,id',
            'fd_amount'   => 'required|numeric|min:1000',
            'durations'   => 'required|integer|in:12,24,36',
            'fd_types'    => 'required|string|in:monthly,onetime',
            'start_date'  => 'required|date',
            'description' => 'nullable|string',
            'remarks'     => 'nullable|string',
            'percentage'  => 'nullable|numeric|min:0',
        ]);
       //dd($validated);
       //dd($client->id);
        $start_date = Carbon::parse($validated['start_date']);
        $end_date   = $start_date->copy()->addMonths($validated['durations']);

        $principal     = $validated['fd_amount'];
        $duration_year = $validated['durations'] / 12;
        $interest_rate = $validated['percentage'] ?? 0;

        if ($validated['fd_types'] === 'monthly') {
            $total_paid = $principal * pow((1 + ($interest_rate / 100) / 12), 12 * $duration_year);
        } else {
            $total_paid = $principal + ($principal * $interest_rate * $duration_year / 100);
        }

        $fd = FD::create([
            'currency_id' => $validated['currency_id'],
            'customer_id' => $client->id,
            'durations'   => $validated['durations'],
            'fd_types'    => $validated['fd_types'],
            'fd_amount'   => $principal,
            'total_paid'  => round($total_paid, 2),
            'due_Amount'  => round($total_paid - $principal, 2),
            'start_date'  => $start_date,
            'end_date'    => $end_date,
            'description' => $validated['description'] ?? null,
            'remarks'     => $validated['remarks'] ?? null,
            'percentage'  => $interest_rate,
            'status' => 0,
        ]);

           if($request->fd_types == 'monthly') {

                $months = $request->durations;
                $monthlyInterest = ($principal * $interest_rate / 100) / 12;

                for ($i = 1; $i <= $months; $i++) {

                    $payDate = $start_date->copy()->addMonths($i);

                    DB::table('fd_schedules')->insert([
                        'fd_id'        => $fd->id,
                        'month_no'     => $i,
                        'payment_date' => $payDate,
                        'principal'    => round($principal / $months, 2),
                        'interest'     => round($monthlyInterest, 2),
                        'total_amount' => round(($principal / $months) + $monthlyInterest, 2),
                        'status'       => 'pending',
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }
            }

        return response()->json([
            'status'  => 'success',
            'message' => 'FD created successfully.',
            'data'    => $fd
        ]);
    }


    public function index(Request $request)
    {

        $client = $this->getClientFromToken($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        //dd($client);
        $fds = FD::with('currency')
            ->where('customer_id', $client->id)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'FD list fetched successfully.',
            'data' => $fds
        ]);
    }


//    public function show(Request $request ,  $id){

//     $client = $this->getClientFromToken($request);
//     if (!$client) {
//         return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
//     }
//     dd($client);
//    }

   public function show(Request $request, $id)
   {
       //dd($request->all(), $id);
       $client = $this->getClientFromToken($request);
       if (!$client) {
           return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
       }
       //dd($client);
       $fd = FD::with('currency')
           ->where('customer_id', $client->id)
           ->find($id);

       if (!$fd) {
           return response()->json(['status' => 'error', 'message' => 'FD not found.'], 404);
       }

       // Monthly schedule
       $schedule = [];
       if ($fd->fd_types === 'monthly') {
        //dd('sjdbdsd');

           $principal = $fd->fd_amount;
           $months = $fd->durations;
           $monthlyInterest = ($principal * $fd->percentage / 100) / 12;
           $startDate = Carbon::parse($fd->start_date);
           $today = Carbon::today();

           for ($i = 1; $i <= $months; $i++) {
               $paymentDate = $startDate->copy()->addMonths($i);
               $monthDiff = $startDate->diffInMonths($today) + 1;

               if ($i < $monthDiff) {
                   $status = 'paid';
               } elseif ($i == $monthDiff) {
                   $status = 'due';
               } else {
                   $status = 'upcoming';
               }

               $schedule[] = [
                   'month'        => $i,
                   'payment_date' => $paymentDate->format('d M Y'),
                   'interest'     => number_format($monthlyInterest, 2),
                   'total'        => number_format(($principal / $months) + $monthlyInterest, 2),
                   'status'       => $status,
               ];
           }
       }

       return response()->json([
           'status'   => 'success',
           'message'  => 'FD details fetched successfully.',
           'data'     => [
               'fd' => $fd,
               'schedule' => $schedule
           ]
       ]);
   }




}
