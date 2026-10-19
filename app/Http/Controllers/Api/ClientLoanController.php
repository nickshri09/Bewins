<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\LoanProduct;
use App\Models\Currency;
use App\Models\CustomField;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
// use App\Utilities\Calculator;
use App\Utilities\LoanCalculator as Calculator;
use Illuminate\Support\Facades\Log;

class ClientLoanController extends Controller
{
    private function getClientFromToken(Request $request)
    {
        $token = $request->bearerToken();
        $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

        if (!$accessToken || !$accessToken->tokenable_type) {
            return null;
        }

        return $accessToken->tokenable_type::withoutGlobalScopes()
            ->find($accessToken->tokenable_id)
            ->load('branch');
    }

    // public function store(Request $request)
    // {
    //     $client = $this->getClientFromToken($request);
    //     // dd($client);
    //     //dd($request->all());

    //     if (!$client) {
    //         return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
    //     }
    //    //dd($client);
    //     @ini_set('max_execution_time', 0);
    //     @set_time_limit(0);

    //     $loanProduct = LoanProduct::find($request->loan_product_id);
    //     if (!$loanProduct) {
    //         return response()->json(['status' => 'error', 'message' => 'Invalid Loan Product'], 400);
    //     }

    //     $min_amount = $loanProduct->minimum_amount;
    //     $max_amount = $loanProduct->maximum_amount;

    //     // ---------- VALIDATION ----------
    //     $validationRules = [
    //         'loan_product_id'        => 'required',
    //         'currency_id'            => 'required',
    //         'first_payment_date'     => 'required|date',
    //         'release_date'           => 'required|date',
    //         'applied_amount'         => "required|numeric|min:$min_amount|max:$max_amount",
    //         'late_payment_penalties' => 'nullable|numeric',
    //         'attachment'             => 'nullable|mimes:jpeg,jpg,png,pdf,doc,docx,zip|max:8192',
    //         'address'                => 'nullable|string',
    //     ];

    //     $validator = Validator::make($request->all(), $validationRules);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $validator->errors()->first()
    //         ], 422);
    //     }

    //     // ---------- ATTACHMENT UPLOAD ----------
    //     $attachment = "";
    //     if ($request->hasfile('attachment')) {
    //         $file       = $request->file('attachment');
    //         $attachment = time() . $file->getClientOriginalName();
    //         $file->move(public_path() . "/uploads/media/", $attachment);
    //     }

    //    // dd($validator);
    //     DB::beginTransaction();

    //     // ---------- CREATE LOAN ----------
    //     $loan = new Loan();
    //     $loan->loan_id                = $loanProduct->loan_id_prefix . $loanProduct->starting_loan_id;
    //     $loan->loan_product_id        = $request->loan_product_id;
    //     $loan->borrower_id            = $client->id;   // API Client
    //     $loan->currency_id            = $request->currency_id;
    //     $loan->first_payment_date     = $request->first_payment_date;
    //     $loan->release_date           = $request->release_date;
    //     $loan->applied_amount         = $request->applied_amount;
    //     $loan->late_payment_penalties = $request->late_payment_penalties;
    //     $loan->attachment             = $attachment;
    //     $loan->remarks                = $request->remarks;
    //     $loan->created_user_id        = $client->id;
    //     $loan->branch_id              = $client->branch_id;
    //     $loan->address                = $request->address;
    //     $loan->description            = $request->description;

    //     $loan->save();

    //     // ---------- GENERATE REPAYMENTS ----------
    //     $calculator = new Calculator(
    //         $loan->applied_amount,
    //         $loan->first_payment_date,
    //         $loan->loan_product->interest_rate,
    //         $loan->loan_product->term,
    //         $loan->loan_product->term_period,
    //         $loan->late_payment_penalties
    //     );

    //     if ($loan->loan_product->interest_type == 'flat_rate') {
    //         $repayments = $calculator->get_flat_rate();
    //     } else if ($loan->loan_product->interest_type == 'fixed_rate') {
    //         $repayments = $calculator->get_fixed_rate();
    //     } else if ($loan->loan_product->interest_type == 'mortgage') {
    //         $repayments = $calculator->get_mortgage();
    //     } else if ($loan->loan_product->interest_type == 'one_time') {
    //         $repayments = $calculator->get_one_time();
    //     } else if ($loan->loan_product->interest_type == 'reducing_amount') {
    //         $repayments = $calculator->get_reducing_amount();
    //     }

    //     $loan->total_payable = $calculator->payable_amount;
    //     $loan->save();

    //     // UPDATE LOAN ID COUNTER
    //     $loanProduct->increment('starting_loan_id');

    //     DB::commit();

    //     // ---------- RESPONSE ----------
    //     return response()->json([
    //         'status'  => 'success',
    //         'message' => 'Loan successfully created',
    //         'loan'    => $loan
    //     ]);
    // }

    public function store(Request $request)
    {
        $client = $this->getClientFromToken($request);

        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        $loanProduct = LoanProduct::active()->find($request->loan_product_id);
        if (!$loanProduct) {
            return response()->json(['status' => 'error', 'message' => 'Invalid Loan Product'], 400);
        }
        if (!$loanProduct->hasValidTerm()) {
            return response()->json(['status' => 'error', 'message' => 'This loan product has an invalid repayment term.'], 422);
        }

        $min_amount = $loanProduct->minimum_amount;
        $max_amount = $loanProduct->maximum_amount;

        // ---------- VALIDATION ----------
        $validationRules = [
            'loan_product_id' => 'required',
            'currency_id' => 'required',
            'first_payment_date' => 'required|date',
            'release_date' => 'required|date',
            'applied_amount' => "required|numeric|min:$min_amount|max:$max_amount",
            'interest_rate' => 'nullable|numeric|min:0',
            'late_payment_penalties' => 'nullable|numeric',
            'attachment' => 'nullable|mimes:jpeg,jpg,png,pdf,doc,docx,zip|max:8192',
            'address' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        // ---------- ATTACHMENT UPLOAD ----------
        $attachment = "";
        if ($request->hasfile('attachment')) {
            $file = $request->file('attachment');
            $attachment = time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/media/", $attachment);
        }

        DB::beginTransaction();

        $interestRate = $request->interest_rate;
        if (!$interestRate) {
            $expectedReturn = InvestmentExpectedReturn::where('status', 1)
                ->where('type', 'loan')
                ->first();

            if ($expectedReturn) {
                $interestRate = $expectedReturn->expected_return;
            } else {

                $interestRate = $loanProduct->interest_rate;
            }
        }

        // ---------- CREATE LOAN ----------
        $loan = new Loan();
        $loan->loan_product_id = $request->loan_product_id;
        $loan->borrower_id = $client->id;   // API Client
        $loan->currency_id = $request->currency_id;
        $loan->first_payment_date = $request->first_payment_date;
        $loan->release_date = $request->release_date;
        $loan->applied_amount = $request->applied_amount;
        $loan->interest_rate = $interestRate;
        $loan->late_payment_penalties = $request->late_payment_penalties;
        $loan->attachment = $attachment;
        $loan->remarks = $request->remarks;
        $loan->created_user_id = $client->id;
        $loan->branch_id = $client->branch_id;
        $loan->address = $request->address;
        $loan->description = $request->description;

        $loan->save();

        $calculator = new Calculator(
            $loan->applied_amount,
            $loan->first_payment_date,
            $interestRate,
            $loan->loan_product->term,
            $loan->loan_product->term_period,
            $loan->late_payment_penalties
        );

        if ($loan->loan_product->interest_type == 'flat_rate') {
            $repayments = $calculator->get_flat_rate();
        } else if ($loan->loan_product->interest_type == 'fixed_rate') {
            $repayments = $calculator->get_fixed_rate();
        } else if ($loan->loan_product->interest_type == 'mortgage') {
            $repayments = $calculator->get_mortgage();
        } else if ($loan->loan_product->interest_type == 'one_time') {
            $repayments = $calculator->get_one_time();
        } else if ($loan->loan_product->interest_type == 'reducing_amount') {
            $repayments = $calculator->get_reducing_amount();
        }

        $loan->total_payable = $calculator->payable_amount;
        $loan->save();

        DB::commit();

        // ---------- RESPONSE ----------
        return response()->json([
            'status' => 'success',
            'message' => 'Loan successfully created',
            'loan' => $loan
        ]);
    }
    // public function show_all_loans(Request $request)
    // {
    //     $client = $this->getClientFromToken($request);

    //     if (!$client) {
    //         return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
    //     }
    //   // dd($client);
    //     $loans = Loan::where('borrower_id', $client->id)
    //         ->with('loan_product', 'currency')
    //         ->orderBy('id', 'desc')
    //         ->get();
    //     //dd($loans);
    //     return response()->json([
    //         'status' => 'success',
    //         'data'   => $loans
    //     ]);
    // }

    // new
    // public function show_all_loans(Request $request)
    // {
    //     $client = $this->getClientFromToken($request);

    //     if (!$client) {
    //         return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
    //     }

    //     // GET /api/loans?status=pending
    //     $status = strtolower($request->status);

    //     // dd($status);
    //     // Correct Status Map (as per your code)

    //     // $statusMap =[
    //     //     0 => 'pending',
    //     //     1 => 'approved',
    //     //     2 => 'completed',
    //     //     3 => 'cancelled',
    //     // ];

    //     $statusMap = [
    //         'pending'=>0,
    //         'approved'=>1,
    //         'completed'=>2,
    //         'cancelled'=>3,
    //     ];

    //     // dd($status ,$statusMap );
    //     $query = Loan::where('borrower_id', $client->id)
    //         ->with('loan_product', 'currency')
    //         ->orderBy('id', 'desc');

    //     // Add status filter ONLY if valid
    //     if (!empty($status) && isset($statusMap[$status])) {
    //         $query->where('status', $statusMap[$status]);
    //     }

    //     $loans = $query->get();

    //     return response()->json([
    //         'status' => 'success',
    //         'data'   => $loans
    //     ]);
    // }


    // new one
    public function show_all_loans(Request $request)
    {
        $client = $this->getClientFromToken($request);

        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        $status = strtolower($request->status);
        $keyword = $request->search; // search keyword
        // dd($keyword);
        $statusMap = [
            'pending' => 0,
            'approved' => 1,
            'completed' => 2,
            'cancelled' => 3,
        ];

        $query = Loan::where('borrower_id', $client->id)
            ->with('loan_product', 'currency')
            ->orderBy('id', 'desc');

        // Status filter
        if (!empty($status) && isset($statusMap[$status])) {
            $query->where('status', $statusMap[$status]);
        }

        // Keyword search
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('loan_id', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhere('remarks', 'like', "%{$keyword}%")
                    ->orWhere('address', 'like', "%{$keyword}%");

            });
        }

        $loans = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $loans
        ]);
    }


    // public function show_all_loans(Request $request)
// {
//     $client = $this->getClientFromToken($request);

    //     if (!$client) {
//         return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
//     }

    //     $status = strtolower($request->status);
//     $keyword = $request->keyword;

    //     $statusMap = [
//         'pending'   => 0,
//         'approved'  => 1,
//         'completed' => 2,
//         'cancelled' => 3,
//     ];

    //     $query = Loan::query()
//         ->where('borrower_id', $client->id)
//         ->with(['loan_product', 'currency', 'borrower'])
//         ->orderByDesc('id');

    //     // Status filter
//     if (!empty($status) && isset($statusMap[$status])) {
//         $query->where('status', $statusMap[$status]);
//     }

    //     // Keyword search
//     if (!empty($keyword)) {
//         $query->where(function($q) use ($keyword) {
//             $q->where('loan_id', 'like', "%{$keyword}%")
//               ->orWhere('description', 'like', "%{$keyword}%")
//               ->orWhere('remarks', 'like', "%{$keyword}%")
//               ->orWhere('address', 'like', "%{$keyword}%")
//               ->orWhereHas('borrower', function($bq) use ($keyword) {
//                   $bq->whereNotNull('id') // avoid null borrower
//                      ->where(function($sub) use ($keyword) {
//                          $sub->where('first_name', 'like', "%{$keyword}%")
//                              ->orWhere('last_name', 'like', "%{$keyword}%");
//                      });
//               });
//         });
//     }

    //     $loans = $query->get();

    //     return response()->json([
//         'status' => 'success',
//         'data'   => $loans
//     ]);
// }







    // public function show_single_loan(Request $request, $loan_id)
    // {
    //     $client = $this->getClientFromToken($request);

    //     if (!$client) {
    //         return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
    //     }

    //     // TEST ONLY (Optional)
    //     // dd(Loan::withoutGlobalScopes()->find($loan_id));

    //     // Borrower ka loan hi fetch karo
    //     $loan = Loan::withoutGlobalScopes()
    //         ->where('id', $loan_id)
    //         ->where('borrower_id', $client->id)
    //         ->with([
    //             'loan_product',
    //             'currency',
    //             'borrower',
    //             'repayments',
    //             'next_payment',
    //             'collaterals',
    //             'guarantors',
    //             'payments'
    //         ])
    //         ->first();

    //     if (!$loan) {
    //         return response()->json([
    //             'status'  => 'error',
    //             'message' => 'Loan not found'
    //         ], 404);
    //     }

    //     return response()->json([
    //         'status' => 'success',
    //         'data'   => $loan
    //     ]);
    // }




    public function show_single_loan(Request $request, $loan_id)
    {
        Log::info("STEP 1: show_single_loan() called", ['loan_id' => $loan_id]);

        $client = $this->getClientFromToken($request);
        Log::info("STEP 2: Client fetched from token", ['client' => $client]);

        if (!$client) {
            Log::error("STEP 3: Unauthenticated user");
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        Log::info("STEP 4: Fetching loan without global scopes");

        // $loan = Loan::withoutGlobalScopes()
        //     ->where('id', $loan_id)
        //     ->where('borrower_id', $client->id)
        //     ->with([
        //         'loan_product',
        //         'currency',
        //         'borrower',
        //         'repayments',
        //         'next_payment',
        //         'collaterals',
        //         'guarantors',
        //         'payments'
        //     ])
        //     ->first();

        $loan = Loan::withoutGlobalScopes()
            ->where('id', $loan_id)
            ->where('borrower_id', $client->id)->with([
                    'loan_product',
                    'currency',
                    'repayments',
                    'next_payment',
                    'collaterals',
                    'guarantors',
                    'payments'
                ])

            ->first();


        Log::info("SINGLE LOAN RAW", ['loan' => $loan]);


        Log::info("STEP 5: Loan query result", ['loan' => $loan]);

        if (!$loan) {
            Log::warning("STEP 6: Loan not found", [
                'loan_id' => $loan_id,
                'borrower_id' => $client->id
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Loan not found'
            ], 404);
        }

        Log::info("STEP 7: Loan found successfully", ['loan_id' => $loan->id]);

        return response()->json([
            'status' => 'success',
            'data' => $loan
        ]);
    }



    // public function loan_products(){

    //     $products=LoanProduct::all();
//     // dd($products);
//     return response()->json([
//         'status'=> 'success',
//         'data' => $products
//     ]);
// }

    public function loan_products(Request $request)
    {

        $client = $this->getClientFromToken($request);

        if (!$client) {
            return response()->json([
                'status' => 'error! Token Not Found',
                'message' => 'Unauthenticated'
            ], 401);
        }

        $products = LoanProduct::all();

        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }














}
