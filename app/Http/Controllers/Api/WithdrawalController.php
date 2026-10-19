<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\Member;
use App\Models\MemberWallet;
use App\Models\MemberWalletTransaction;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    /**
     * Helper: resolve Member from authenticated user
     */
    private function resolveMember($user)
    {
        return ($user instanceof Member) ? $user : Member::where('user_id', $user->id)->first();
    }

    /**
     * GET /api/client/investments/list
     * Member ke active investments with full details
     */
    public function investmentList(Request $request)
    {
        $user = $request->user();
        $member = $this->resolveMember($user);

        if (!$member) {
            return response()->json(['status' => 'error', 'message' => 'Member record not found.'], 404);
        }

        $investments = Investment::where('customer_id', $member->id)
            ->where('status', 1) // Active/Approved only
            ->get()
            ->map(function ($inv) {
                // Aggregate wallet transactions for this investment
                $totals = MemberWalletTransaction::where('investment_id', $inv->id)
                    ->selectRaw("
                        COALESCE(SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END), 0) as total_interest_earned,
                        COALESCE(SUM(COALESCE(gross_amount, 0)), 0) as total_gross_interest,
                        COALESCE(SUM(COALESCE(tds_amount, 0)), 0) as total_tds_deducted
                    ")
                    ->first();

                $totalInterestEarned = (float) ($totals->total_interest_earned ?? 0);
                $totalGross          = (float) ($totals->total_gross_interest ?? 0);
                $totalTds            = (float) ($totals->total_tds_deducted ?? 0);

                return [
                    'id'                   => $inv->id,
                    'investment_type'      => $inv->investment_type,
                    'contribution_type'    => $inv->contribution_type,
                    'investment_amount'    => (float) $inv->investment_amount,
                    'expected_return'      => (float) $inv->expected_return,
                    'tds_percent'          => (float) $inv->tds_percent,
                    'start_date'           => $inv->start_date?->format('Y-m-d'),
                    'maturity_date'        => $inv->maturity_date?->format('Y-m-d'),
                    'status'               => $inv->status_label,
                    'total_gross_interest' => $totalGross,
                    'total_tds_deducted'   => $totalTds,
                    'total_net_interest'   => $totalInterestEarned,
                    'total_value'          => round((float) $inv->investment_amount + $totalInterestEarned, 2),
                ];
            });

        $cumulativePrincipal = (float) Investment::where('customer_id', $member->id)
            ->where('status', 1)
            ->sum('investment_amount');

        return response()->json([
            'status'  => 'success',
            'message' => 'Investment list fetched successfully.',
            'cumulative_investment' => $cumulativePrincipal,
            'data'    => $investments,
        ]);
    }

    /**
     * POST /api/client/withdrawal/request
     * Raise a withdrawal request (wallet or investment)
     */
    public function createRequest(Request $request)
    {
        $request->validate([
            'type'          => 'required|in:wallet,investment',
            'amount'        => 'required|numeric|min:1',
            'investment_id' => 'required_if:type,investment|nullable|exists:investments,id',
        ]);

        $user   = $request->user();
        $member = $this->resolveMember($user);

        if (!$member) {
            return response()->json(['status' => 'error', 'message' => 'Member record not found.'], 404);
        }

        if (!$member->allow_withdrawal) {
            return response()->json(['status' => 'error', 'message' => 'Withdrawals are currently disabled for this account.'], 403);
        }

        try {
            $wr = DB::transaction(function () use ($request, $member) {
                if ($request->type === 'wallet') {
                    // Lock the wallet so concurrent requests cannot reserve the same balance.
                    $wallet = MemberWallet::where('member_id', $member->id)->lockForUpdate()->first();
                    $balance = (float) ($wallet->balance ?? 0);
                    $reserved = (float) WithdrawalRequest::where('member_id', $member->id)
                        ->where('type', 'wallet')->where('status', 'pending')->sum('amount');
                    $available = max(0, $balance - $reserved);
                } else {
                    $investment = Investment::where('id', $request->investment_id)
                        ->where('customer_id', $member->id)->where('status', 1)
                        ->lockForUpdate()->first();

                    if (!$investment) {
                        throw new \DomainException('Investment not found or not active.');
                    }

                    $reserved = (float) WithdrawalRequest::where('investment_id', $investment->id)
                        ->where('type', 'investment')->where('status', 'pending')->sum('amount');
                    $available = max(0, (float) $investment->investment_amount - $reserved);
                }

                if ((float) $request->amount > $available) {
                    throw new \DomainException('Insufficient available balance. Available: ₹'.number_format($available, 2, '.', ''));
                }

                return WithdrawalRequest::create([
                    'member_id' => $member->id,
                    'type' => $request->type,
                    'investment_id' => $request->type === 'investment' ? $request->investment_id : null,
                    'amount' => $request->amount,
                    'status' => 'pending',
                ]);
            });
        } catch (\DomainException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Withdrawal request submitted successfully. Awaiting admin approval.',
            'data'    => [
                'request_id' => $wr->id,
                'type'       => $wr->type,
                'amount'     => (float) $wr->amount,
                'status'     => $wr->status,
            ],
        ], 201);
    }

    /**
     * GET /api/client/withdrawal/requests
     * Member ki saari requests
     */
    public function myRequests(Request $request)
    {
        $user   = $request->user();
        $member = $this->resolveMember($user);

        if (!$member) {
            return response()->json(['status' => 'error', 'message' => 'Member record not found.'], 404);
        }

        $requests = WithdrawalRequest::where('member_id', $member->id)
            ->with(['investment:id,investment_type,investment_amount,contribution_type'])
            ->orderByDesc('id')
            ->paginate((int) $request->get('per_page', 20));

        return response()->json([
            'status'  => 'success',
            'message' => 'Withdrawal requests fetched successfully.',
            'data'    => $requests,
        ]);
    }
}
