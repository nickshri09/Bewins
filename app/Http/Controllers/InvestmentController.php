<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Investment;
use App\Models\InvestmentExpectedReturn;
use App\Models\Member;
use App\Models\MemberWallet;
use App\Models\MemberWalletTransaction;
use App\Models\CommissionRecord;
use App\Models\WalletTransaction;
use App\Services\AgreementEmailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class InvestmentController extends Controller
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

    public function index()
    {
        // $investments = Investment::with('customer', 'currency')->get();
        $userType = Auth::user()->user_type;
        if ($userType == 'admin') {
            $investments = Investment::with('customer', 'currency')
                ->withSum(['wallet_transactions as total_net_interest' => function ($query) {
                    $query->where('type', 'credit');
                }], 'amount')
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $investments = Investment::with('customer', 'currency')
                ->withSum(['wallet_transactions as total_net_interest' => function ($query) {
                    $query->where('type', 'credit');
                }], 'amount')
                ->where('created_user_id', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->get();
        }
        return view('investments.index', compact('investments'));
    }

    public function create()
    {
        if (auth()->user()->user_type == 'admin') {
            $customers = Member::all();
        } else {
            $customers = Member::where('created_by', auth()->user()->id)->get();
        }
        //
        // $customers = Member::all();

        $currencies = Currency::all();
        $expectedReturns = InvestmentExpectedReturn::where('status', 1)->where('type', 'investment')->get();

        // dd($expectedReturns);
        return view('investments.create', compact('customers', 'currencies', 'expectedReturns'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'customer_id' => 'required|exists:members,id',
            'investment_amount' => 'required|numeric|min:1000',
            'investment_type' => 'required|string',
            'contribution_type' => 'required|in:onetime,monthly',
            'risk_level' => 'required|in:low,medium,high',
            'start_date' => 'required|date',
            'maturity_date' => 'nullable|date|after_or_equal:start_date',
            'duration' => 'nullable|integer|min:1',
            'currency_id' => 'required|exists:currency,id',
            'description' => 'nullable|string|max:255',
            'remark' => 'nullable|string|max:255',
            'expected_return' => 'nullable|numeric'
        ]);

        $startDate = Carbon::parse($request->start_date);

        //  Duration & Maturity
        if ($request->investment_type !== 'mutual_fund' && $request->duration) {
            $maturityDate = $startDate->copy()->addMonths($request->duration);
        } else {
            $maturityDate = $request->maturity_date
                ? Carbon::parse($request->maturity_date)
                : null;
        }

        $durationYears = $request->duration
            ? $request->duration / 12
            : ($maturityDate ? $startDate->diffInMonths($maturityDate) / 12 : 0);

        $principal = $request->investment_amount;
        $rate = $request->expected_return;

        //  Maturity Amount Calculation (Using Simple Interest for consistency with schedule)
        // If contribution_type is monthly, we still typically calculate flat interest for payouts in this system model
        $maturityAmount = $principal + ($principal * $rate * $durationYears / 100);

        $investment = Investment::create([
            'customer_id' => $request->customer_id,
            'investment_amount' => $request->investment_amount,
            'investment_type' => $request->investment_type,
            'contribution_type' => $request->contribution_type,
            'risk_level' => $request->risk_level,
            'expected_return' => $request->expected_return,
            'start_date' => $startDate,
            'maturity_date' => $maturityDate,
            'duration' => $request->duration,
            'currency_id' => $request->currency_id,
            'description' => $request->description,
            'remarks' => $request->remark,
            'status' => 0,  // Pending
            'tds_percent' => get_option('investment_tds_percent', 0),
            'created_user_id' => Auth::id(),
            'maturity_amount' => round($maturityAmount, 2),
        ]);

        $this->generateRepaymentSchedule($investment);

        return redirect()->route('investments.index')->with('success', 'Investment added successfully');
    }

    public function show($id)
    {
        $investment = Investment::with(['customer', 'currency'])->findOrFail($id);

        if ($investment->status == 1) {
            $this->generateRepaymentSchedule($investment);
        }

        // Fetch repayments with collector info
        $schedule = DB::table('investment_repayments')
            ->leftJoin('users', 'investment_repayments.collected_by', '=', 'users.id')
            ->where('investment_id', $investment->id)
            ->select('investment_repayments.*', 'users.name as collector_name')
            ->orderBy('month_no')
            ->get();

        $wallet_transactions = \App\Models\MemberWalletTransaction::where('investment_id', $id)
            ->orderBy('id', 'desc')
            ->get();

        return view('investments.view', compact('investment', 'schedule', 'wallet_transactions'));
    }

    public function edit(Investment $investment)
    {
        // Check if locked and not admin
        if (Auth::user()->user_type != 'admin' && $investment->is_locked == 1) {
            return redirect()->route('investments.index')->with('error', 'This investment is locked and cannot be edited.');
        }

        $customers = Member::all();
        $currencies = Currency::all();
        $expectedReturns = InvestmentExpectedReturn::where('status', 1)->where('type', 'investment')->get();

        // Check if any payment is already done
        $payments_count = DB::table('investment_repayments')
            ->where('investment_id', $investment->id)
            ->where('status', 'paid')
            ->count();

        return view('investments.edit', compact('investment', 'customers', 'currencies', 'expectedReturns', 'payments_count'));
    }

    public function update(Request $request, Investment $investment)
    {
        // Check if locked and not admin
        if (Auth::user()->user_type != 'admin' && $investment->is_locked == 1) {
            return redirect()->route('investments.index')->with('error', 'This investment is locked and cannot be edited.');
        }

        $request->validate([
            'customer_id' => 'required|exists:members,id',
            'investment_amount' => 'required|numeric|min:1000',
            'investment_type' => 'required|string',
            'contribution_type' => 'required|in:onetime,monthly',
            'risk_level' => 'required|in:low,medium,high',
            'start_date' => 'required|date',
            'maturity_date' => 'required|date|after_or_equal:start_date',
            'currency_id' => 'required|exists:currency,id',
            'description' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:255',
            'expected_return' => 'nullable|numeric'
        ]);

        $updateData = [
            'customer_id' => $request->customer_id,
            'investment_type' => $request->investment_type,
            'contribution_type' => $request->contribution_type,
            'risk_level' => $request->risk_level,
            'expected_return' => $request->percentage, // The view uses 'percentage' name for expected_return select
            'start_date' => $request->start_date,
            'maturity_date' => $request->maturity_date,
            'currency_id' => $request->currency_id,
            'description' => $request->description,
            'remarks' => $request->remarks,
        ];

        // Only update investment_amount if no payments have been made
        $payments_count = DB::table('investment_repayments')
            ->where('investment_id', $investment->id)
            ->where('status', 'paid')
            ->count();

        if ($payments_count == 0) {
            $updateData['investment_amount'] = $request->investment_amount;
        }

        $investment->update($updateData);

        return redirect()->route('investments.index')->with('success', 'Investment updated successfully');
    }

    public function destroy(Investment $investment)
    {
        DB::transaction(function () use ($investment) {
            // Delete related repayments
            DB::table('investment_repayments')->where('investment_id', $investment->id)->delete();
            
            // Delete related agreement documents
            $investment->agreement_documents()->delete();
            
            // Delete the investment
            $investment->delete();
        });

        return redirect()->route('investments.index')->with('success', 'Investment deleted successfully');
    }

    // Get repayment details for modal
    public function getRepaymentDetails($repayment_id)
    {
        $rep = DB::table('investment_repayments')->find($repayment_id);
        // dd($rep);
        if (!$rep) {
            return response()->json(['status' => false, 'msg' => 'Repayment not found']);
        }

        return response()->json([
            'id' => $rep->id,
            'principal_amount' => $rep->principal,
            'monthly_interest' => $rep->profit,
            'total_payout' => $rep->total_amount,
            'payment_date' => $rep->payment_date,
            'late_penalty' => $rep->late_penalty,
        ]);
    }

    public function payRepayment(Request $request)
    {
        $rep = DB::table('investment_repayments')
            ->where('id', $request->emi_id)
            ->first();

        if (!$rep) {
            return response()->json([
                'status' => false,
                'msg' => 'Repayment not found'
            ]);
        }

        $paymentMode = $request->razorpay_payment_id ? 'razorpay' : 'cash';

        // Mark EMI as paid
        DB::table('investment_repayments')
            ->where('id', $request->emi_id)
            ->update([
                'late_penalty'          => $request->late_penalty ?? 0,
                'payment_mode'          => $paymentMode,
                'collected_by'          => Auth::id(), // ID of the person who marked it paid
                'admin_received_status' => $paymentMode == 'razorpay' ? 1 : 0, // Razorpay is direct, Cash is pending admin receipt
                'admin_received_at'     => $paymentMode == 'razorpay' ? now() : null,
                'transaction_id'        => $request->razorpay_payment_id ?? null,
                'status'                => 'paid',
                'paid_date'             => now(),
                'updated_at'            => now()
            ]);

        // Check if all repayments are paid
        $pendingCount = DB::table('investment_repayments')
            ->where('investment_id', $rep->investment_id)
            ->where('status', '!=', 'paid')
            ->count();

        // If last payment completed
        if ($pendingCount == 0) {
            DB::table('investments')
                ->where('id', $rep->investment_id)
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

    public function approve($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $inv = Investment::whereKey($id)->lockForUpdate()->firstOrFail();
                if ((int) $inv->status !== 0) {
                    throw new \DomainException('Only pending investments can be approved.');
                }

                $inv->status = 1;
                $inv->tds_percent = get_option('investment_tds_percent', 0);
                $inv->save();
                $this->generateRepaymentSchedule($inv);
                $this->creditCommissions($inv);
            });
        } catch (\DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        $emailResult = app(AgreementEmailService::class)->sendInvestmentAgreement(Investment::findOrFail($id));
        if ($emailResult['sent']) {
            return back()->with('success', 'Investment approved. '.$emailResult['message']);
        }

        return back()->with('error', 'Investment approved, but no agreement email was sent. '.$emailResult['message']);
    }

    private function creditCommissions(Investment $investment): void
    {
        $member = Member::withoutGlobalScopes()->find($investment->customer_id);
        if (!$member) {
            return;
        }

        $baseAmount = round((float) $investment->investment_amount, 2);
        // A client referred by a user's RBU code earns the direct-agent
        // commission for that user. Fall back to the assigned/creating user
        // for existing clients that have no user referral code on record.
        $directAgentId = $member->referred_by_user_id ?: ($member->assigned_user_id ?: $member->created_by);
        if ($directAgentId) {
            $this->creditCommission($investment, $member, 'user', $directAgentId, 0, $baseAmount,
                (float) get_option('commission_direct_agent_rate', '2.00'));
        }

        $referrer = $member->referrer;
        for ($level = 1; $level <= 3 && $referrer; $level++) {
            $rate = (float) get_option('commission_level_' . $level . '_rate', [1 => '0.50', 2 => '0.30', 3 => '0.20'][$level]);
            $this->creditCommission($investment, $member, 'member', $referrer->id, $level, $baseAmount, $rate);
            $referrer = $referrer->referrer;
        }
    }

    private function creditCommission(Investment $investment, Member $source, string $recipientType, int $recipientId, int $level, float $baseAmount, float $rate): void
    {
        $amount = round($baseAmount * $rate / 100, 2);
        if ($amount <= 0) {
            return;
        }

        $agent = $recipientType === 'user'
            ? \App\Models\User::whereKey($recipientId)->lockForUpdate()->first()
            : null;
        if ($recipientType === 'user' && !$agent) {
            return;
        }

        $record = CommissionRecord::firstOrCreate([
            'investment_id' => $investment->id,
            'level' => $level,
            'recipient_type' => $recipientType,
            'recipient_id' => $recipientId,
        ], [
            'source_member_id' => $source->id,
            'base_amount' => $baseAmount,
            'rate_percent' => $rate,
            'amount' => $amount,
        ]);

        if (!$record->wasRecentlyCreated) {
            return;
        }

        if ($recipientType === 'member') {
            $wallet = MemberWallet::firstOrCreate(['member_id' => $recipientId], ['balance' => 0]);
            $wallet->increment('balance', $amount);
            MemberWalletTransaction::create([
                'member_wallet_id' => $wallet->id,
                'member_id' => $recipientId,
                'investment_id' => $investment->id,
                'type' => 'credit',
                'gross_amount' => $amount,
                'tds_amount' => 0,
                'amount' => $amount,
                'note' => 'Referral commission, level ' . $level . ', investment ' . $investment->investment_code,
                'created_by' => auth()->id(),
            ]);
            return;
        }

        $agent->wallet_amount = (float) $agent->wallet_amount + $amount;
        $agent->save();
        WalletTransaction::create([
            'user_id' => $agent->id,
            'amount' => $amount,
            'type' => 'credit',
            'note' => 'Direct agent commission for investment ' . $investment->investment_code,
            'created_by' => auth()->id(),
        ]);
    }

    public function reject($id)
    {
        $inv = Investment::findOrFail($id);

        if ($inv->status != 0) {
            return back()->with('error', 'Only pending investments can be rejected');
        }

        $inv->status = 2;
        $inv->save();

        return back()->with('success', 'Investment Rejected');
    }

    /**
     * Cash Collection Tracker for Admin
     */
    public function cashCollectionTracker()
    {
        $collections = DB::table('investment_repayments')
            ->join('investments', 'investment_repayments.investment_id', '=', 'investments.id')
            ->join('members', 'investments.customer_id', '=', 'members.id')
            ->join('users', 'investment_repayments.collected_by', '=', 'users.id')
            ->where('investment_repayments.payment_mode', 'cash')
            ->select(
                'investment_repayments.*',
                DB::raw("CONCAT(members.first_name, ' ', members.last_name) as investor_name"),
                'members.mobile as investor_mobile',
                'users.name as employee_name',
                'users.mobile_number as employee_mobile'
            )
            ->orderBy('investment_repayments.admin_received_status', 'asc')
            ->orderBy('investment_repayments.paid_date', 'desc')
            ->get();

        return view('investments.cash_tracker', compact('collections'));
    }

    /**
     * Admin verifies cash receipt from employee
     */
    public function verifyCashCollection($id)
    {
        DB::table('investment_repayments')
            ->where('id', $id)
            ->update([
                'admin_received_status' => 1,
                'admin_received_at'     => now(),
                'updated_at'            => now()
            ]);

        return response()->json([
            'status' => true,
            'msg'    => 'Cash collection verified successfully'
        ]);
    }

    /**
     * Download Cash Collection Receipt
     */
    public function downloadReceipt($id)
    {
        $collection = DB::table('investment_repayments')
            ->join('investments', 'investment_repayments.investment_id', '=', 'investments.id')
            ->join('members', 'investments.customer_id', '=', 'members.id')
            ->join('users', 'investment_repayments.collected_by', '=', 'users.id')
            ->join('currency', 'investments.currency_id', '=', 'currency.id')
            ->where('investment_repayments.id', $id)
            ->select(
                'investment_repayments.*',
                DB::raw("CONCAT(members.first_name, ' ', members.last_name) as investor_name"),
                'members.mobile as investor_mobile',
                'members.address as investor_address',
                'users.name as employee_name',
                'users.mobile_number as employee_mobile',
                'currency.name as currency_name',
                // 'currency.symbol as currency_symbol'
            )
            ->first();

        if (!$collection) {
            return back()->with('error', 'Collection record not found');
        }

        $pdf = Pdf::loadView('investments.pdf_receipt', compact('collection'));
        return $pdf->download('Receipt-' . $collection->id . '.pdf');
    }
    /**
     * Toggle Investment Lock status
     */
    public function toggleLock($id)
    {
        if (Auth::user()->user_type != 'admin') {
            return response()->json(['status' => false, 'msg' => 'Unauthorized access'], 403);
        }

        $investment = Investment::findOrFail($id);
        $closing = !$investment->is_locked;
        $investment->is_locked = $closing ? 1 : 0;
        $investment->closed_at = $closing ? now() : null;
        $investment->save();

        if ($closing) {
            $certificateAvailable = false;
            try {
                [$member, $certificatePath] = $this->generateClosureCertificate($investment);
                $certificateAvailable = true;
                $this->emailClosureCertificate($investment, $member, $certificatePath);

                return response()->json([
                    'status' => true,
                    'is_locked' => true,
                    'certificate_available' => true,
                    'email_sent' => true,
                    'msg' => 'BCS closed. Closure certificate emailed to ' . $member->email . '.',
                ]);
            } catch (\Throwable $exception) {
                Log::error('BCS closure certificate email failed', [
                    'investment_id' => $investment->id,
                    'error' => $exception->getMessage(),
                ]);

                return response()->json([
                    'status' => true,
                    'is_locked' => true,
                    'certificate_available' => $certificateAvailable,
                    'email_sent' => false,
                    'msg' => 'BCS closed, but the certificate email failed. Use Resend Email after checking the client email and mail settings.',
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'is_locked' => $investment->is_locked,
            'certificate_available' => false,
            'email_sent' => false,
            'msg' => 'BCS closure status removed.',
        ]);
    }

    public function downloadClosureCertificate($id)
    {
        abort_unless(Auth::user()->user_type === 'admin', 403);

        $investment = Investment::with('currency')->findOrFail($id);
        abort_unless($investment->is_locked, 404, 'This BCS is not closed.');

        try {
            [$member, $path] = $this->ensureClosureCertificate($investment);
        } catch (\Throwable $exception) {
            Log::error('BCS closure certificate generation failed', [
                'investment_id' => $investment->id,
                'error' => $exception->getMessage(),
            ]);
            return back()->with('error', 'Could not generate the BCS closure certificate. Please check the customer record and try again.');
        }

        return Storage::disk('local')->download($path, 'BCS-Closure-' . $investment->investment_code . '.pdf');
    }

    public function resendClosureCertificate($id)
    {
        abort_unless(Auth::user()->user_type === 'admin', 403);

        $investment = Investment::with('currency')->findOrFail($id);
        if (!$investment->is_locked) {
            return back()->with('error', 'Only closed BCS records have a closure certificate.');
        }

        try {
            [$member, $path] = $this->ensureClosureCertificate($investment);
            $this->emailClosureCertificate($investment, $member, $path);
        } catch (\Throwable $exception) {
            Log::error('BCS closure certificate resend failed', [
                'investment_id' => $investment->id,
                'error' => $exception->getMessage(),
            ]);
            return back()->with('error', 'Could not email the closure certificate. Check the customer email and mail settings, then try again.');
        }

        return back()->with('success', 'BCS closure certificate emailed to ' . $member->email . '.');
    }

    private function ensureClosureCertificate(Investment $investment): array
    {
        $member = Member::withoutGlobalScopes()->findOrFail($investment->customer_id);

        $needsClosedAt = blank($investment->closed_at);
        if ($needsClosedAt) {
            $investment->closed_at = now();
        }

        $path = $investment->closure_certificate_path;
        if (blank($path) || !Storage::disk('local')->exists($path)) {
            $path = $this->generateClosureCertificate($investment)[1];
        } elseif ($needsClosedAt) {
            $investment->save();
        }

        return [$member, $path];
    }

    private function generateClosureCertificate(Investment $investment): array
    {
        $member = Member::withoutGlobalScopes()->findOrFail($investment->customer_id);
        $investment->loadMissing('currency');

        if (blank($investment->closed_at)) {
            $investment->closed_at = now();
        }

        $filename = preg_replace('/[^A-Za-z0-9_-]/', '-', (string) ($investment->investment_code ?: $investment->id));
        $path = 'bcs_closure_certificates/' . $filename . '.pdf';
        $pdf = Pdf::loadView('investments.closure_certificate', [
            'investment' => $investment,
            'member' => $member,
        ])->setPaper('A4', 'portrait');

        Storage::disk('local')->put($path, $pdf->output());
        $investment->closure_certificate_path = $path;
        $investment->save();

        return [$member, $path];
    }

    private function emailClosureCertificate(Investment $investment, Member $member, string $path): void
    {
        if (blank($member->email)) {
            throw new \RuntimeException('The customer does not have an email address.');
        }

        Mail::send('emails.bcs_closure_certificate', [
            'member' => $member,
            'investment' => $investment,
        ], function ($message) use ($member, $investment, $path) {
            $message->to($member->email, $member->name)
                ->subject('BCS Closure Certificate - ' . $investment->investment_code)
                ->attach(Storage::disk('local')->path($path), [
                    'as' => 'BCS-Closure-' . $investment->investment_code . '.pdf',
                    'mime' => 'application/pdf',
                ]);
        });

        $investment->closure_certificate_sent_at = now();
        $investment->save();
    }
}
