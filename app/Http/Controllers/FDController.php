<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\FD;
use App\Models\InvestmentExpectedReturn;
use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FDController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->status;

        $currentUserId = auth()->id();

        $userType = auth()->user()->user_type;

        if ($userType == 'admin') {
            $fds = FD::with('currency', 'member')
                ->when($status !== null, function ($q) use ($status) {
                    $q->where('status', $status);
                })
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $fds = FD::with('currency', 'member')
                ->when($status !== null, function ($q) use ($status) {
                    $q->where('status', $status);
                })
                ->where('created_by', $currentUserId)
                ->orderBy('id', 'desc')
                ->get();
        }

        return view('fd.index', compact('fds', 'status'));
    }

    public function create()
    {
        // $currencies = Currency::all();
        $currencies = Currency::where('name', 'INR')->get();

        // $users = User::all();
        // dd( Member::all());
        if (auth()->user()->user_type == 'admin') {
            $customers = Member::all();
        } else {
            $customers = Member::where('created_by', auth()->user()->id)->get();
        }
        // $customers=Member::where('created');
        $expectedReturns = InvestmentExpectedReturn::where('status', 1)->where('type', 'fd')->get();
        // dd($expectedReturns);
        return view('fd.create', compact('currencies', 'customers', 'expectedReturns'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'currency_id' => 'required|exists:currency,id',
            'fd_amount' => 'required|numeric|min:1000',
            'durations' => 'required|integer|in:12,24,36',  // 1yr,2yr,3yr
            'fd_types' => 'required|string|in:monthly,onetime',
            'start_date' => 'required|date',
            'description' => 'string|nullable',
            'remarks' => 'string|nullable',
            'percentage' => 'nullable|numeric|min:0',
        ]);

        // dd($request->percentage);

        $start_date = Carbon::parse($request->start_date);
        $end_date = $start_date->copy()->addMonths($request->durations);

        $principal = $request->fd_amount;
        $duration_year = $request->durations / 12;
        // $interest_rate = 10;

        $interest_rate = $request->percentage;

        if ($request->fd_types == 'monthly') {
            // Using Simple Interest for consistency with the schedule generation
            $total_paid = $principal + ($principal * $interest_rate * $duration_year / 100);
        } else {
            $total_paid = $principal + ($principal * $interest_rate * $duration_year / 100);
        }

        // dd($principal);
        // $createdById = Auth::user()->id;
        // dd($createdById);

        $fd = FD::create([
            'currency_id' => $request->currency_id,
            'customer_id' => $request->customer_id,
            'durations' => $request->durations,
            'fd_types' => $request->fd_types,
            'fd_amount' => $principal,
            'total_paid' => round($total_paid, 2),
            // 'due_Amount'  => round($total_paid - $principal, 2),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'description' => $request->description,
            'remarks' => $request->remarks,
            'percentage' => $request->percentage,
            'status' => 0,
            'created_by' => auth()->user()->id,
        ]);

        if ($request->fd_types == 'monthly') {
            $months = $request->durations;
            $monthlyInterest = ($principal * $interest_rate / 100) / 12;
            $monthlyPrincipal = $principal / $months;

            $totalPrincipalScheduled = 0;
            $totalInterestScheduled = 0;

            for ($i = 1; $i <= $months; $i++) {
                $payDate = $start_date->copy()->addMonths($i);

                // Handle rounding for the last month
                if ($i == $months) {
                    $prin = round($principal - $totalPrincipalScheduled, 2);
                    // $int = round(($total_paid - $principal) - $totalInterestScheduled, 2);
                    // Use simpler logic for last interest to avoid penny issues from total_paid rounding?
                    // Actually, let's keep interest uniform and just adjust principal to ensure it sums to exact principal
                    $int = round($monthlyInterest, 2);
                } else {
                    $prin = round($monthlyPrincipal, 2);
                    $int = round($monthlyInterest, 2);
                }

                $totalPrincipalScheduled += $prin;
                $totalInterestScheduled += $int;

                DB::table('fd_schedules')->insert([
                    'fd_id' => $fd->id,
                    'month_no' => $i,
                    'payment_date' => $payDate,
                    'principal' => $prin,
                    'interest' => $int,
                    'total_amount' => $prin + $int,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($request->fd_types == 'onetime') {
            // One Time Payment at the end (Maturity)
            DB::table('fd_schedules')->insert([
                'fd_id' => $fd->id,
                'month_no' => 1,
                'payment_date' => $end_date,
                'principal' => $principal,
                'interest' => round($total_paid - $principal, 2),
                'total_amount' => round($total_paid, 2),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('fd.index')->with('success', 'FD Created Successfully!');
    }

    public function show($id)
    {
        // dd($id);
        // $fd = FD::with('currency', 'user')->findOrFail($id);
        $fd = FD::with('currency', 'member')->findOrFail($id);
        // dd($fd);

        $interestRate = $fd->percentage;

        // $schedule = [];

        // if ($fd->fd_types === 'monthly') {
        //     $principal = $fd->fd_amount;
        //     $months = $fd->durations;
        //     $monthlyInterest = ($principal * $interestRate / 100) / 12;

        //     $startDate = \Carbon\Carbon::parse($fd->start_date);
        //     $today = \Carbon\Carbon::today();

        //     if ($today->gt($startDate)) {
        //         $status = 'paid';
        //     } elseif ($today->eq($startDate)) {
        //         $status = 'due';
        //     } else {
        //         $status = 'upcoming';
        //     }

        //     for ($i = 1; $i <= $months; $i++) {
        //         $paymentDate = $startDate->copy()->addMonths($i);

        //          // Determine status
        //     // if ($today->gt($paymentDate)) {
        //     //     $status = 'paid';
        //     // } elseif ($today->eq($paymentDate)) {
        //     //     $status = 'due';
        //     // } else {
        //     //     $status = 'upcoming';
        //     // }

        //     //  // Updated status logic
        //     //     if ($paymentDate->lt($today)) {
        //     //         $status = 'due';        // Back date
        //     //     } elseif ($paymentDate->eq($today)) {
        //     //         $status = 'paid';       // Today
        //     //     } else {
        //     //         $status = 'upcoming';   // Future date
        //     //     }

        //      // Month difference from start_date
        //         $monthDiff = $startDate->diffInMonths($today) + 1; // +1 for 1-based month

        //         if ($i < $monthDiff) {
        //             $status = 'paid';       // Already passed relative to start_date
        //         } elseif ($i == $monthDiff) {
        //             $status = 'due';        // Current month
        //         } else {
        //             $status = 'upcoming';   // Future month
        //         }

        //         $schedule[] = [
        //             'month' => $i,
        //             'payment_date' => $paymentDate->format('d M Y'),
        //             'principal' => number_format($principal / $months, 2),
        //             'interest' => number_format($monthlyInterest, 2),
        //             'total' => number_format(($principal / $months) + $monthlyInterest, 2),
        //             'status' => $status,
        //         ];
        //     }
        // }

        // dd($schedule);

        $schedule = DB::table('fd_schedules')
            ->where('fd_id', $fd->id)
            ->orderBy('month_no')
            ->get();

        // dd($schedule);
        return view('fd.view', compact('fd', 'schedule'));
    }

    // public function approve($id)
    // {
    //     $fd = FD::findOrFail($id);
    //     $fd->status = 'active';
    //     $fd->save();

    //     return redirect()->back()->with('success', 'FD Approved Successfully!');
    // }

    public function approve($id)
    {
        $fd = FD::findOrFail($id);

        $fd->status = 1;  // approved
        $fd->save();

        return back()->with('success', 'FD Approved Successfully!');
    }

    public function reject($id)
    {
        $fd = FD::findOrFail($id);

        if ($fd->status != 0) {
            return back()->with('error', 'Only pending FDs can be rejected');
        }

        $fd->status = 2;  // rejected
        $fd->save();

        return back()->with('success', 'FD Rejected Successfully!');
    }

    public function withdraw($id)
    {
        $fd = FD::findOrFail($id);

        if (Carbon::now()->lt(Carbon::parse($fd->end_date))) {
            return redirect()->back()->with('error', 'FD is not yet matured.');
        }

        $fd->status = 'withdrawn';
        $fd->save();

        return redirect()->back()->with('success', 'FD Withdrawn Successfully!');
    }

    public function edit($id)
    {
        // dd($id);

        $fd = Fd::findOrFail($id);
        // / dd($fd);
        $currencies = Currency::all();
        //    $users = User::all();
        $customers = Member::all();
        $expectedReturns = InvestmentExpectedReturn::where('status', 1)->where('type', 'fd')->get();
        return view('fd.edit', compact('fd', 'currencies', 'customers', 'expectedReturns'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'currency_id' => 'required|exists:currency,id',
            'fd_amount' => 'required|numeric|min:1000',
            'durations' => 'required|integer|in:12,24,36',
            'fd_types' => 'required|string|in:monthly,onetime',
            'start_date' => 'required|date',
            'end_date' => 'date',
            'description' => 'string|nullable',
            'remarks' => 'string|nullable',
            'percentage' => 'nullable|numeric|min:0',
        ]);

        $start_date = Carbon::parse($request->start_date);
        // $end_date   = $start_date->copy()->addMonths($request->durations);
        $end_date = Carbon::parse($request->end_date);

        $principal = $request->fd_amount;
        $duration_year = $request->durations / 12;
        // $interest_rate = 10;
        $interest_rate = $request->percentage;

        if ($request->fd_types == 'monthly') {
            $total_paid = $principal * pow((1 + ($interest_rate / 100) / 12), 12 * $duration_year);
        } else {
            $total_paid = $principal + ($principal * $interest_rate * $duration_year / 100);
        }

        $fd = FD::findOrFail($id);

        $fd->update([
            'currency_id' => $request->currency_id,
            // 'user_id'     => auth()->id(),
            // 'user_id'     => $request->user_id,
            'customer_id' => $request->customer_id,
            'durations' => $request->durations,
            'fd_types' => $request->fd_types,
            'fd_amount' => $principal,
            'total_paid' => round($total_paid, 2),
            'due_Amount' => round($total_paid - $principal, 2),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'description' => $request->description,
            'remarks' => $request->remarks,
            'percentage' => $request->percentage,
        ]);

        return redirect()->route('fd.index')->with('success', 'FD updated successfully!');
    }

    public function getDetails($id)
    {
        $schedule = DB::table('fd_schedules')->find($id);
        // dd($schedule);

        if (!$schedule) {
            return response()->json(['status' => false, 'msg' => 'Repayment not found']);
        }
        // dd($schedule);

        return response()->json([
            'id' => $schedule->id,
            'principal_amount' => $schedule->principal,
            'monthly_interest' => $schedule->interest,
            'total_payout' => $schedule->total_amount,
            'payment_date' => $schedule->payment_date,
            'late_penalty' => $schedule->late_penalty,
        ]);
    }

    public function payRepayment(Request $request)
    {
        // dd($request->all());
        $rep = DB::table('fd_schedules')->where('id', $request->emi_id)->first();

        if (!$rep) {
            return response()->json([
                'status' => false,
                'msg' => 'Repayment not found'
            ]);
        }

        // Mark EMI as paid
        DB::table('fd_schedules')
            ->where('id', $request->emi_id)
            ->update([
                'late_penalty' => $request->late_penalty ?? 0,
                // 'total_amount'     => $rep->principal,
                'payment_mode' => $request->razorpay_payment_id ? 'razorpay' : 'cash',
                'transaction_id' => $request->razorpay_payment_id ?? null,
                'status' => 'paid',
                'paid_date' => now(),
                'updated_at' => now()
            ]);

        // Check if all repayments are paid
        $pendingCount = DB::table('fd_schedules')
            ->where('fd_id', $rep->fd_id)
            ->where('status', '!=', 'paid')
            ->count();

        // If last payment completed
        if ($pendingCount == 0) {
            DB::table('table_fd')
                ->where('id', $rep->fd_id)
                ->update([
                    'disbused' => 'ready to disbursed',
                    'updated_at' => now()
                ]);
        }

        return response()->json([
            'status' => true,
            'msg' => 'Payment successful'
        ]);
    }
}
