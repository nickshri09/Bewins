<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Order;
use App\Models\User;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Auth;
use PDF;

class WalletController extends Controller
{
    public function __construct()
    {
        // Set timezone to India/Mumbai
        $timezone = 'Asia/Kolkata';
        date_default_timezone_set($timezone);
        config(['app.timezone' => $timezone]);
    }

    public function index()
    {
        $users = User::all();
        $currency = Currency::where('base_currency', 1)->first();
        if (!$currency) {
            $currency = Currency::first();
        }
        return view('backend.admin.wallets.index', compact('users', 'currency'));
    }

    public function addMoneyView()
    {
        $user = FacadesAuth::user();

        // ------------------------------------------------------------------
        // Razorpay Smart Collect: Create Virtual Account for QR Code
        // ------------------------------------------------------------------
        if (empty($user->virtual_account_id) || empty($user->razorpay_customer_id)) {
            $keyId = env('RAZORPAY_KEY');
            $keySecret = env('RAZORPAY_SECRET');

            if ($keyId && $keySecret) {
                try {
                    $api = new Api($keyId, $keySecret);

                    // 1. Create Customer (if not exists)
                    if (empty($user->razorpay_customer_id)) {
                        $customerData = [
                            'name' => $user->name,
                            'email' => $user->email,
                            'contact' => $user->mobile_number,
                            'fail_existing' => '0'
                        ];
                        $customer = $api->customer->create($customerData);
                        $user->razorpay_customer_id = $customer->id;
                        $user->save();
                    }

                    // 2. Create Virtual Account
                    if (empty($user->virtual_account_id)) {
                        $virtualAccountData = [
                            'receivers' => [
                                'types' => [
                                    'vpa'
                                ]
                            ],
                            'description' => 'Wallet Recharge - ' . $user->name,
                            'customer_id' => $user->razorpay_customer_id
                            // 'close_by' => ... (Optional)
                        ];

                        $virtualAccount = $api->virtualAccount->create($virtualAccountData);

                        $user->virtual_account_id = $virtualAccount->id;

                        // 3. Extract Details
                        if (isset($virtualAccount->receivers) && count($virtualAccount->receivers) > 0) {
                            foreach ($virtualAccount->receivers as $receiver) {
                                if ($receiver->entity == 'qr_code' && isset($receiver->short_url)) {
                                    $user->qr_code_url = $receiver->short_url;
                                }
                                if ($receiver->entity == 'vpa' && isset($receiver->address)) {
                                    $user->vpa_address = $receiver->address;
                                }
                            }
                        }

                        $user->save();
                    }
                } catch (\Exception $e) {
                    Log::error('Razorpay Smart Collect Error: ' . $e->getMessage());
                    // We don't stop the page load, but QR won't show.
                }
            }
        }

        return view('layouts.menus.add_money', compact('user'));
    }

    public function addMoney(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string'
        ]);

        $user = User::findOrFail($request->user_id);
        $user->wallet_amount += $request->amount;
        $user->save();

        WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'type' => 'credit',
            'note' => $request->note,
            'created_by' => Auth::id(),
            'created_at' => Carbon::now()->setTimezone('Asia/Kolkata')
        ]);

        return redirect()->back()->with('success', 'Wallet amount added successfully');
    }

    public function history(Request $request)
    {
        $query = WalletTransaction::with(['user', 'createdBy'])
            ->orderBy('created_at', 'desc');

        // Date Range Filter: Convert user input (IST) to UTC for DB query
        if ($request->daterange) {
            $dates = explode(' - ', $request->daterange);
            $startLocal = Carbon::parse($dates[0])->startOfDay()->setTimezone('Asia/Kolkata');
            $endLocal = Carbon::parse($dates[1])->endOfDay()->setTimezone('Asia/Kolkata');

            $query->whereBetween('created_at', [
                $startLocal->copy()->setTimezone('UTC'),
                $endLocal->copy()->setTimezone('UTC')
            ]);
        }

        // User Filter
        if ($request->user) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user . '%');
            });
        }

        // Type Filter
        if ($request->type) {
            $query->where('type', $request->type);
        }

        // Amount Range Filter
        if ($request->min_amount !== null && $request->min_amount !== '') {
            $query->where('amount', '>=', $request->min_amount);
        }
        if ($request->max_amount !== null && $request->max_amount !== '') {
            $query->where('amount', '<=', $request->max_amount);
        }

        $transactions = $query->paginate(20);

        // PDF Export
        if ($request->export == 'pdf') {
            $pdf = PDF::loadView('backend.admin.wallets.history_pdf', compact('transactions'));
            return $pdf->download('wallet-transactions.pdf');
        }

        return view('backend.admin.wallets.history', compact('transactions'));
    }

    public function walletTransaction()
    {
        $user = Auth::user();
        $walletAmount = $user->wallet_amount;
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('layouts.menus.wallet', compact('walletAmount', 'transactions'));
    }

    public function getUserDetails(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // --- 1. Orders ---
        $ordersQuery = Order::where('user_id', $id);
        if ($request->order_status != '') {
            $ordersQuery->where('order_status', $request->order_status);
        }
        if ($request->date_from != '') {
            $ordersQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to != '') {
            $ordersQuery->whereDate('created_at', '<=', $request->date_to);
        }
        $orders = $ordersQuery->get();

        // --- 2. Loans, FDs, Investments ---
        // Using 'created_by' / 'created_user_id' to find records created by the Agent/User.

        $loans = \App\Models\Loan::where('created_user_id', $id)
            ->when($request->date_from, function ($q) use ($request) {
                return $q->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                return $q->whereDate('created_at', '<=', $request->date_to);
            })
            ->get();

        $fds = \App\Models\FD::where('created_by', $id)
            ->when($request->date_from, function ($q) use ($request) {
                return $q->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                return $q->whereDate('created_at', '<=', $request->date_to);
            })
            ->get();

        $investments = \App\Models\Investment::with('currency')
            ->where('created_user_id', $id)
            ->when($request->date_from, function ($q) use ($request) {
                return $q->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                return $q->whereDate('created_at', '<=', $request->date_to);
            })
            ->get();

        // Combine data
        $mixedData = [];

        foreach ($orders as $order) {
            $mixedData[] = [
                'id' => $order->id,
                'type' => 'Order',
                'amount' => $order->main_service_fee,
                'date' => $order->created_at,
                'details' => $order
            ];
        }

        foreach ($loans as $loan) {
            $mixedData[] = [
                'id' => $loan->id,
                'type' => 'Loan',
                'amount' => $loan->applied_amount,  // or principal_amount
                'date' => $loan->created_at,
                'details' => $loan
            ];
        }

        foreach ($fds as $fd) {
            $mixedData[] = [
                'id' => $fd->id,
                'type' => 'FD',
                'amount' => $fd->fd_amount,
                'date' => $fd->created_at,
                'details' => $fd
            ];
        }

        foreach ($investments as $inv) {
            $mixedData[] = [
                'id' => $inv->id,
                'type' => 'Investment',
                'amount' => $inv->investment_amount,
                'date' => $inv->created_at,
                'details' => $inv
            ];
        }

        // Sort by Date Descending
        usort($mixedData, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // Calculate Totals per Category
        $totalOrderAmount = $orders->sum('main_service_fee');
        $totalLoanAmount = $loans->sum('applied_amount');  // Assuming 'applied_amount' is the field for Loan value
        $totalFDAmount = $fds->sum('fd_amount');
        $totalInvestmentAmount = $investments->sum('investment_amount');

        // Grand Total of all User's Business/Sales/Creations
        $totalAmount = $totalOrderAmount + $totalLoanAmount + $totalFDAmount + $totalInvestmentAmount;

        // Calculate Commission on the Grand Total
        $commission = ($totalAmount * $user->percent) / 100;

        // This 'final_total' logic seems to be 'Total Business + Commission' or user expectation?
        // Usually, 'Final Payout' might just be Commission.
        // But if the view shows "Final Total Amount", we maintain the pattern: Total Business + Commission (or whatever the previous logic implied).
        // Previous logic: $finalTotal = $totalOrderAmount + $commission;
        // So we follow suit:
        $finalTotal = $totalAmount + $commission;

        return response()->json([
            'name' => $user->name,
            'percent' => $user->percent,
            'total_order_amount' => $totalAmount,  // "Total Order Amount" label in frontend, but now represents 'Total Business Amount'
            'commission' => $commission,
            'final_total' => $finalTotal,
            'mixed_data' => $mixedData
        ]);
    }

    public function getUserAccountDetails(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user)
            return response()->json(['error' => 'User not found'], 404);

        // --- 1. Orders ---
        $ordersQuery = Order::where('user_id', $id);
        if ($request->order_status != '') {
            $ordersQuery->where('order_status', $request->order_status);
        }
        if ($request->date_from != '') {
            $ordersQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to != '') {
            $ordersQuery->whereDate('created_at', '<=', $request->date_to);
        }
        $orders = $ordersQuery->get();
        $totalOrderAmount = $orders->sum('main_service_fee');

        // --- 2. Loans ---
        $totalLoanAmount = \App\Models\Loan::where('created_user_id', $id)
            ->when($request->date_from, function ($q) use ($request) {
                return $q->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                return $q->whereDate('created_at', '<=', $request->date_to);
            })
            ->sum('applied_amount');

        // --- 3. FDs ---
        $totalFDAmount = \App\Models\FD::where('created_by', $id)
            ->when($request->date_from, function ($q) use ($request) {
                return $q->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                return $q->whereDate('created_at', '<=', $request->date_to);
            })
            ->sum('fd_amount');

        // --- 4. Investments ---
        $totalInvestmentAmount = \App\Models\Investment::where('created_user_id', $id)
            ->when($request->date_from, function ($q) use ($request) {
                return $q->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                return $q->whereDate('created_at', '<=', $request->date_to);
            })
            ->sum('investment_amount');

        // Grand Total
        $totalAmount = $totalOrderAmount + $totalLoanAmount + $totalFDAmount + $totalInvestmentAmount;

        // Calculate Commission
        $commission = ($totalAmount * $user->percent) / 100;

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'wallet_amount' => $user->wallet_amount,
            'commission' => $commission,
            'account_number' => $user->account_number,
            'ifsc_code' => $user->ifsc_code,
            'bank_name' => $user->bank_name,
        ]);
    }

    public function payCOD(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user)
            return response()->json(['error' => 'User not found'], 404);

        $amount = $request->amount;
        if ($amount == 0 || $amount === null) {
            return response()->json([
                'success' => false,
                'message' => 'There is no commission to pay for this transaction'
            ]);
        }

        WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => 'credit',
            'note' => 'COD Payment to ' . $user->name,
            'created_by' => FacadesAuth::id(),
            'created_at' => Carbon::now()->setTimezone('Asia/Kolkata')
        ]);

        return response()->json(['success' => true, 'message' => 'COD Payment recorded']);
    }

    public function handleRazorpaySuccess(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'amount' => 'required|numeric|min:1'
        ]);

        try {
            // Payment successful, add money to user's wallet
            $user = Auth::user();
            $amount = $request->amount;

            // Update user wallet
            $user->wallet_amount += $amount;
            $user->save();

            // Create wallet transaction
            WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'credit',
                'note' => 'Razorpay payment - Payment ID: ' . $request->razorpay_payment_id,
                'created_by' => $user->id,
                'created_at' => Carbon::now()->setTimezone('Asia/Kolkata')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment successful and wallet updated',
                'new_balance' => $user->wallet_amount
            ]);
        } catch (\Exception $e) {
            Log::error('Wallet update error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Wallet update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handle Razorpay Webhook for Smart Collect (Virtual Account Payments)
     */
    public function handleWebhook(Request $request)
    {
        try {
            // 1. Get webhook payload
            $payload = $request->getContent();
            $signature = $request->header('X-Razorpay-Signature');

            // 2. Verify webhook signature (Security)
            $webhookSecret = env('RAZORPAY_WEBHOOK_SECRET');

            if ($webhookSecret) {
                $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);

                if ($signature !== $expectedSignature) {
                    Log::warning('Razorpay Webhook: Invalid signature');
                    return response()->json(['status' => 'invalid signature'], 400);
                }
            }

            // 3. Parse payload
            $data = json_decode($payload, true);

            if (!$data || !isset($data['event'])) {
                Log::error('Razorpay Webhook: Invalid payload');
                return response()->json(['status' => 'invalid payload'], 400);
            }

            // 4. Handle event: virtual_account.credited
            if ($data['event'] === 'virtual_account.credited') {
                $payment = $data['payload']['payment']['entity'] ?? null;
                $virtualAccount = $data['payload']['virtual_account']['entity'] ?? null;

                if (!$payment || !$virtualAccount) {
                    Log::error('Razorpay Webhook: Missing payment or virtual_account data');
                    return response()->json(['status' => 'missing data'], 400);
                }

                $virtualAccountId = $virtualAccount['id'];
                $amount = $payment['amount'] / 100;  // Convert paisa to rupees
                $paymentId = $payment['id'];

                // 5. Find user by virtual_account_id
                $user = User::where('virtual_account_id', $virtualAccountId)->first();

                if (!$user) {
                    Log::error("Razorpay Webhook: User not found for VA: {$virtualAccountId}");
                    return response()->json(['status' => 'user not found'], 404);
                }

                // 6. Check if payment already processed (idempotency)
                $existingTransaction = WalletTransaction::where('note', 'LIKE', "%Payment ID: {$paymentId}%")->first();

                if ($existingTransaction) {
                    Log::info("Razorpay Webhook: Duplicate payment ignored: {$paymentId}");
                    return response()->json(['status' => 'already processed'], 200);
                }

                // 7. Update wallet
                $user->wallet_amount += $amount;
                $user->save();

                // 8. Create transaction record
                WalletTransaction::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'type' => 'credit',
                    'note' => "Smart Collect Payment - Payment ID: {$paymentId}",
                    'created_by' => $user->id,
                    'created_at' => Carbon::now()->setTimezone('Asia/Kolkata')
                ]);

                Log::info("Razorpay Webhook: ₹{$amount} credited to User #{$user->id} ({$user->name})");

                return response()->json(['status' => 'success'], 200);
            }

            // Other events (ignore for now)
            Log::info("Razorpay Webhook: Unhandled event - {$data['event']}");
            return response()->json(['status' => 'event ignored'], 200);
        } catch (\Exception $e) {
            Log::error('Razorpay Webhook Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Test Webhook - For Development/Testing Only
     * Simulates a Razorpay webhook payment
     */
    public function testWebhook(Request $request)
    {
        $user = FacadesAuth::user();

        if (!$user || !$user->virtual_account_id) {
            return response()->json([
                'success' => false,
                'message' => 'No virtual account found. Please visit Add Money page first.'
            ], 400);
        }

        // Simulate webhook payload
        $amount = $request->get('amount', 10);  // Default ₹10
        $paymentId = 'pay_test_' . time();

        try {
            // Check duplicate
            $existingTransaction = WalletTransaction::where('note', 'LIKE', "%Payment ID: {$paymentId}%")->first();

            if ($existingTransaction) {
                return response()->json(['success' => false, 'message' => 'Already processed'], 200);
            }

            // Update wallet
            $user->wallet_amount += $amount;
            $user->save();

            // Create transaction
            WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'credit',
                'note' => "TEST Payment - Payment ID: {$paymentId}",
                'created_by' => $user->id,
                'created_at' => Carbon::now()->setTimezone('Asia/Kolkata')
            ]);

            Log::info("TEST Webhook: ₹{$amount} credited to User #{$user->id} ({$user->name})");

            return response()->json([
                'success' => true,
                'message' => "₹{$amount} added to wallet successfully!",
                'new_balance' => $user->wallet_amount,
                'payment_id' => $paymentId
            ]);
        } catch (\Exception $e) {
            Log::error('Test Webhook Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
