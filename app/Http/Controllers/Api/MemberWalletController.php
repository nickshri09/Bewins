<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MemberWallet;
use App\Models\MemberWalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Validator;
use App\Models\Member;

class MemberWalletController extends Controller
{
    private function walletForMember($member)
    {
        // Check if member is actually a Member model or User model
        $memberId = $member->id;
        if ($member instanceof \App\Models\User) {
            $memberRecord = Member::where('user_id', $member->id)->first();
            if ($memberRecord) {
                $memberId = $memberRecord->id;
            }
        }

        return MemberWallet::firstOrCreate(
            ['member_id' => $memberId],
            ['balance' => 0]
        );
    }

    public function summary(Request $request)
    {
        $user = $request->user();
        $member = ($user instanceof \App\Models\Member) ? $user : \App\Models\Member::where('user_id', $user->id)->first();

        if (!$member) {
            return response()->json(['status' => 'error', 'message' => 'Member record not found.'], 404);
        }

        $wallet = $this->walletForMember($member);

        // Aggregate totals from transactions (Interest related only)
        $totals = MemberWalletTransaction::where('member_id', $member->id)
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'credit' AND investment_id IS NOT NULL THEN amount ELSE 0 END), 0) as total_net_interest")
            ->selectRaw("COALESCE(SUM(CASE WHEN investment_id IS NOT NULL THEN gross_amount ELSE 0 END), 0) as total_gross_interest")
            ->selectRaw("COALESCE(SUM(CASE WHEN investment_id IS NOT NULL THEN tds_amount ELSE 0 END), 0) as total_tds_deducted")
            ->selectRaw("MAX(created_at) as last_transaction_at")
            ->first();

        // Calculate total principal invested (Active investments)
        $totalPrincipal = DB::table('investments')
            ->where('customer_id', $member->id)
            ->where('status', 1) // Approved/Active
            ->sum('investment_amount');

        $totalNetInterest = (float) ($totals->total_net_interest ?? 0);

        return response()->json([
            'status' => 'success',
            'message' => 'Wallet summary fetched successfully.',
            'data' => [
                'member_id' => $member->id,
                'wallet_balance' => (float) $wallet->balance,
                'total_principal_invested' => (float) $totalPrincipal,
                'total_gross_interest' => (float) ($totals->total_gross_interest ?? 0),
                'total_tds_deducted' => (float) ($totals->total_tds_deducted ?? 0),
                'total_net_interest' => $totalNetInterest,
                'total_valuation' => (float) ($totalPrincipal + $totalNetInterest),
                'last_transaction_at' => $totals->last_transaction_at,
                'allow_withdrawal' => (bool) $member->allow_withdrawal,
            ],
        ]);
    }

    public function transactions(Request $request)
    {
        $user = $request->user();
        $member = ($user instanceof \App\Models\Member) ? $user : \App\Models\Member::where('user_id', $user->id)->first();

        if (!$member) {
            return response()->json(['status' => 'error', 'message' => 'Member record not found.'], 404);
        }

        $wallet = $this->walletForMember($member);

        $query = MemberWalletTransaction::with(['wallet'])
            ->where('member_id', $member->id)
            ->orderByDesc('id');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('investment_id')) {
            $query->where('investment_id', $request->investment_id);
        }

        $perPage = (int) $request->get('per_page', 20);
        $transactions = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Wallet transactions fetched successfully.',
            'data' => [
                'wallet_balance' => (float) $wallet->balance,
                'transactions' => $transactions,
            ],
        ]);
    }

    public function investmentInterestHistory(Request $request)
    {
        $user = $request->user();
        $member = ($user instanceof \App\Models\Member) ? $user : \App\Models\Member::where('user_id', $user->id)->first();

        if (!$member) {
            return response()->json(['status' => 'error', 'message' => 'Member record not found.'], 404);
        }

        $wallet = $this->walletForMember($member);

        $query = MemberWalletTransaction::query()
            ->where('member_id', $member->id)
            ->where('type', 'credit')
            ->whereNotNull('investment_id')
            ->with(['wallet'])
            ->leftJoin('investments', 'member_wallet_transactions.investment_id', '=', 'investments.id')
            ->select(
                'member_wallet_transactions.*',
                DB::raw('investments.investment_type as investment_type'),
                DB::raw('investments.contribution_type as contribution_type'),
                DB::raw('investments.investment_amount as investment_amount'),
                DB::raw('investments.expected_return as expected_return')
            )
            ->orderByDesc('member_wallet_transactions.id');

        $perPage = (int) $request->get('per_page', 20);
        $history = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Investment interest history fetched successfully.',
            'data' => [
                'wallet_balance' => (float) $wallet->balance,
                'history' => $history,
            ],
        ]);
    }

    /**
     * Add money to wallet via Razorpay
     */
    public function addMoney(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = $request->user();
        $member = ($user instanceof \App\Models\Member) ? $user : \App\Models\Member::where('user_id', $user->id)->first();

        if (!$member) {
            return response()->json(['status' => 'error', 'message' => 'Member record not found.'], 404);
        }

        $razorpayKey = get_option('razorpay_key_id', env('RAZORPAY_KEY'));
        $razorpaySecret = get_option('razorpay_key_secret', env('RAZORPAY_SECRET'));

        if (!$razorpayKey || !$razorpaySecret) {
            return response()->json([
                'status' => 'error',
                'message' => 'Razorpay credentials not configured'
            ], 500);
        }

        try {
            $api = new Api($razorpayKey, $razorpaySecret);

            $orderData = [
                'receipt' => 'wallet_' . time(),
                'amount' => $request->amount * 100, // paise
                'currency' => 'INR',
                'payment_capture' => 1,
                'notes' => [
                    'member_id' => $member->id,
                    'type' => 'wallet_credit'
                ]
            ];

            $razorpayOrder = $api->order->create($orderData);

            return response()->json([
                'status' => 'success',
                'message' => 'Razorpay order created successfully.',
                'data' => [
                    'razorpay_order_id' => $razorpayOrder['id'],
                    'razorpay_key' => $razorpayKey,
                    'amount' => (float)$request->amount,
                    'currency' => 'INR',
                    'name' => $member->first_name . ' ' . $member->last_name,
                    'email' => $member->email,
                    'contact' => $member->mobile
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create Razorpay order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify payment and credit wallet
     */
    public function verifyPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = $request->user();
        $member = ($user instanceof \App\Models\Member) ? $user : \App\Models\Member::where('user_id', $user->id)->first();

        if (!$member) {
            return response()->json(['status' => 'error', 'message' => 'Member record not found.'], 404);
        }

        $razorpaySecret = get_option('razorpay_key_secret', env('RAZORPAY_SECRET'));

        try {
            // Verify signature
            $generatedSignature = hash_hmac('sha256', $request->razorpay_order_id . '|' . $request->razorpay_payment_id, $razorpaySecret);

            if ($generatedSignature !== $request->razorpay_signature) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid payment signature'
                ], 400);
            }

            $wallet = $this->walletForMember($member);

            DB::beginTransaction();

            $wallet->balance += $request->amount;
            $wallet->save();

            MemberWalletTransaction::create([
                'member_wallet_id' => $wallet->id,
                'member_id'        => $member->id,
                'type'             => 'credit',
                'amount'           => $request->amount,
                'gross_amount'     => $request->amount,
                'note'             => 'Razorpay Wallet Recharge. Payment ID: ' . $request->razorpay_payment_id,
                'created_by'       => ($user instanceof \App\Models\Member) ? $user->user_id : $user->id,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment verified and wallet credited successfully.',
                'data' => [
                    'new_balance' => (float)$wallet->balance
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
