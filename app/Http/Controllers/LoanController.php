<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use App\Models\Guarantor;
use App\Models\Loan;
use App\Models\LoanCollateral;
use App\Models\LoanPayment;
use App\Models\LoanProduct;
use App\Models\LoanRepayment;
use App\Models\Member;
use App\Models\Transaction;
use App\Notifications\ApprovedLoanRequest;
use App\Notifications\RejectLoanRequest;
use App\Services\AgreementEmailService;
use App\Utilities\LoanCalculator as Calculator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DataTables;
use App\Models\InvestmentExpectedReturn;

class LoanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        date_default_timezone_set(get_option('timezone', 'Asia/Dhaka'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index(Request $request, $status = '') {
    //     // dd($request->all());
    //     if ($status == 'pending') {
    //         $status = 0;
    //     } else if ($status == 'active') {
    //         $status = 1;
    //     }
    //     // dd('kjh');
    //     return view('backend.loan.list', compact('status'));
    // }

    // public function index(Request $request, $status = '')
    // {
    //     // status convert
    //     if ($status == 'pending') {
    //         $status = 0;
    //     } elseif ($status == 'active') {
    //         $status = 1;
    //     }

    //     $user = Auth::user();

    //     $query = Loan::query();

    //     // Agar normal user hai
    //     if ($user->user_type == 'user') {
    //         // 1. Pehle user ke saare members nikaalo
    //         $memberIds = Member::where('user_id', $user->id)->pluck('id');

    //         // 2. Loan table me borrower_id = member_id match karega
    //         $query->whereIn('borrower_id', $memberIds);
    //     }

    //     // Admin ho to koi filter nahi

    //     // Aage query run karo
    //     $loans = $query->latest()->paginate(20);

    //     return view('backend.loan.list', compact('loans', 'status'));
    // }

    public function index(Request $request, $status = '')
    {
       // Convert status filter to database status
        if ($status == 'pending') {
            $status = 0;
        } elseif ($status == 'active') {
            $status = 1;
        } elseif ($status == 'completed') {
            $status = 2;
        } elseif ($status == 'cancelled') {
            $status = 3;
        }

        $userType = Auth::user()->user_type;
        // dd($userType);
        // dd(Auth::user()->id);

        if ($userType == 'user') {
            $loans = Loan::where('created_user_id', Auth::user()->id)->get();
            // dd('user condition' , $loans);
        } else {
            $loans = Loan::all();
        }

        return view('backend.loan.list', compact('loans', 'status'));

    }

    public function get_table_data(Request $request)
    {
        $user = auth()->user();

        // Base query
        // This page has no branch selector, so ignore a stale branch session
        // filter here. Apply the regular-user ownership rule explicitly below.
        $loans = Loan::withoutGlobalScope('borrower_id')
            ->select('loans.*')
            ->with(['borrower', 'currency', 'loan_product'])
            ->orderBy('loans.id', 'desc');

        // -------------------------------
        // ⭐ ROLE BASED ACCESS CONTROL
        // -------------------------------
        // if ($user->user_type == 'user') {
        //     // 1. User ke saare clients (members) fetch kare
        //     $memberIds = Member::where('user_id', $user->id)->pluck('id');

        //     // 2. Loans filter borrower_id IN members
        //     $loans->whereIn('borrower_id', $memberIds);
        // }

        if ($user->user_type == 'user') {
            $loans->where('created_user_id', $user->id);
        }
        // Admin → koi filter nahi

        return Datatables::eloquent($loans)
            ->filter(function ($query) use ($request) {
                if ($request->has('status')) {
                    $query->where('status', $request->status);
                }
            }, true)
            ->editColumn('borrower.first_name', function ($loan) {
                return trim(($loan->borrower->first_name ?? '') . ' ' . ($loan->borrower->last_name ?? '')) ?: '—';
            })
            ->editColumn('applied_amount', function ($loan) {
                $currencyName = $loan->currency->name ?? null;

                return decimalPlace($loan->applied_amount, $currencyName ? currency($currencyName) : '');
            })
            ->editColumn('status', function ($loan) {
                if ($loan->status == 0) {
                    return show_status(_lang('Pending'), 'warning');
                } elseif ($loan->status == 1) {
                    return show_status(_lang('Approved'), 'success');
                } elseif ($loan->status == 2) {
                    return show_status(_lang('Completed'), 'info');
                } elseif ($loan->status == 3) {
                    return show_status(_lang('Cancelled'), 'danger');
                }

                return '—';
            })
            ->filterColumn('borrower.first_name', function ($query, $keyword) {
                $query->whereHas('borrower', function ($query) use ($keyword) {
                    return $query
                        ->where('first_name', 'like', "{$keyword}%")
                        ->orWhere('last_name', 'like', "{$keyword}%");
                });
            }, true)
            // ACTION BUTTONS
            ->addColumn('action', function ($loan) {
                $user = auth()->user();

                $action = '<form action="' . route('loans.destroy', $loan['id']) . '" class="text-center" method="post">';
                $action .= '<a href="' . route('loans.show', $loan['id']) . '" class="btn btn-primary btn-xs"><i class="ti-eye"></i> ' . _lang('View') . '</a>&nbsp;';
                $action .= '<a href="' . route('loans.edit', $loan['id']) . '" class="btn btn-warning btn-xs"><i class="ti-pencil-alt"></i> ' . _lang('Edit') . '</a>&nbsp;';
                $action .= csrf_field();
                $action .= '<input name="_method" type="hidden" value="DELETE">';

                // ❌ Delete button only for Admin
                if (strtolower($user->user_type) == 'admin') {
                    $action .= '<button class="btn btn-danger btn-xs btn-remove" type="submit">'
                        . '<i class="ti-trash"></i> ' . _lang('Delete') . '</button>';
                }

                $action .= '</form>';

                return $action;
            })
            ->setRowId(function ($loan) {
                return 'row_' . $loan->id;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create(Request $request)
    // {
    //     $alert_col = 'col-lg-8 offset-lg-2';
    //     $customFields = CustomField::where('table', 'loans')
    //         ->where('status', 1)
    //         ->orderBy('id', 'asc')
    //         ->get();
    //     if (!$request->ajax()) {
    //         return view('backend.loan.create', compact('alert_col', 'customFields'));
    //     } else {
    //         return view('backend.loan.modal.create', compact('alert_col', 'customFields'));
    //     }
    // }

    // public function create(Request $request)
    // {

    //     $alert_col = 'col-lg-8 offset-lg-2';

    //     $customFields = CustomField::where('table', 'loans')
    //         ->where('status', 1)
    //         ->orderBy('id', 'asc')
    //         ->get();

    //     // 👇 borrower / customer list
    //     if (auth()->user()->user_type === 'admin') {
    //         $customers = Member::all();
    //     } else {
    //         $customers = Member::where('created_by', auth()->id())->get();
    //     }

    //     if (!$request->ajax()) {
    //         return view('backend.loan.create', compact('alert_col', 'customFields', 'customers'));
    //     } else {
    //         return view('backend.loan.modal.create', compact('alert_col', 'customFields', 'customers'));
    //     }
    // }

    public function create(Request $request)
    {
        $alert_col = 'col-lg-8 offset-lg-2';

        $customFields = CustomField::where('table', 'loans')
            ->where('status', 1)
            ->orderBy('id', 'asc')
            ->get();

        $invalidLoanProducts = LoanProduct::active()->get()
            ->filter(fn (LoanProduct $product) => !$product->hasValidTerm())
            ->values();

        // 👇 Customer List
        if (auth()->user()->user_type === 'admin') {
            $customers = Member::all();
        } else {
            $customers = Member::where('created_by', auth()->id())->get();
        }

        // 👇 यही वह लाइन है जो आपको चाहिए
        // Loan के लिए सभी active expected returns (interest rates) ला रहे हैं
        $expectedReturns = InvestmentExpectedReturn::where('status', 1)->where('type', 'loan')->get();

        if (!$request->ajax()) {
            return view('backend.loan.create', compact('alert_col', 'customFields', 'customers', 'expectedReturns', 'invalidLoanProducts'));
        } else {
            return view('backend.loan.modal.create', compact('alert_col', 'customFields', 'customers', 'expectedReturns', 'invalidLoanProducts'));
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     // dd($request->all());
    //     @ini_set('max_execution_time', 0);
    //     @set_time_limit(0);

    //     $loanProduct = LoanProduct::find($request->loan_product_id);

    //     $min_amount = $loanProduct->minimum_amount;
    //     $max_amount = $loanProduct->maximum_amount;

    //     $validationRules = [
    //         'loan_id' => 'required|unique:loans',
    //         'loan_product_id' => 'required',
    //         'borrower_id' => 'required',
    //         'currency_id' => 'required',
    //         'first_payment_date' => 'required',
    //         'release_date' => 'required',
    //         'applied_amount' => "required|numeric|min:$min_amount|max:$max_amount",
    //         'late_payment_penalties' => 'required|numeric',
    //         'attachment' => 'nullable|mimes:jpeg,JPEG,png,PNG,jpg,doc,pdf,docx,zip|max:8192',  // 8MB = 8192KB
    //         'address' => 'nullable|string',
    //     ];

    //     $validationMessages = [];

    //     // Custom field validation
    //     $customFields = CustomField::where('table', 'loans')
    //         ->orderBy('id', 'desc')
    //         ->get();
    //     $customValidation = generate_custom_field_validation($customFields);

    //     array_merge($validationRules, $customValidation['rules']);
    //     array_merge($validationMessages, $customValidation['messages']);

    //     $validator = Validator::make($request->all(), $validationRules, $validationMessages);

    //     if ($validator->fails()) {
    //         if ($request->ajax()) {
    //             return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
    //         } else {
    //             return redirect()
    //                 ->route('loans.create')
    //                 ->withErrors($validator)
    //                 ->withInput();
    //         }
    //     }

    //     $attachment = '';
    //     if ($request->hasfile('attachment')) {
    //         $file = $request->file('attachment');
    //         $attachment = time() . $file->getClientOriginalName();
    //         $file->move(public_path() . '/uploads/media/', $attachment);
    //     }

    //     DB::beginTransaction();

    //     // Store custom field data
    //     $customFieldsData = store_custom_field_data($customFields);

    //     $loan = new Loan();
    //     $loan->loan_id = $loanProduct->loan_id_prefix . $loanProduct->starting_loan_id;
    //     $loan->loan_product_id = $request->input('loan_product_id');
    //     $loan->borrower_id = $request->input('borrower_id');
    //     $loan->currency_id = $request->input('currency_id');
    //     $loan->first_payment_date = $request->input('first_payment_date');
    //     $loan->release_date = $request->input('release_date');
    //     $loan->applied_amount = $request->input('applied_amount');
    //     $loan->late_payment_penalties = $request->input('late_payment_penalties');
    //     $loan->attachment = $attachment;
    //     $loan->description = $request->input('description');
    //     $loan->remarks = $request->input('remarks');
    //     $loan->created_user_id = Auth::id();
    //     $loan->branch_id = auth()->user()->branch_id;
    //     $loan->custom_fields = json_encode($customFieldsData);
    //     $loan->address = $request->address;
    //     // dd($loan);
    //     $loan->save();

    //     // Create Loan Repayments
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

    //     // Increment Loan ID
    //     $loanProduct->increment('starting_loan_id');

    //     DB::commit();

    //     if (!$request->ajax()) {
    //         return redirect()->route('loans.show', $loan->id)->with('success', _lang('New Loan added successfully'));
    //     } else {
    //         return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('New Loan added successfully'), 'data' => $loan, 'table' => '#loans_table']);
    //     }
    // }


    public function store(Request $request)
    {
        $loanProduct = LoanProduct::active()->find($request->loan_product_id);
        $rejectInvalidProduct = function (string $message) use ($request) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => [$message]], 422);
            }

            return redirect()->route('loans.create')->withErrors(['loan_product_id' => $message])->withInput();
        };

        if (!$loanProduct) {
            return $rejectInvalidProduct(_lang('Select an active loan product.'));
        }

        if (!$loanProduct->hasValidTerm()) {
            return $rejectInvalidProduct(_lang('This loan product has an invalid repayment term. Edit the product and set a term between 1 and 600 periods with a valid period.'));
        }

        $min_amount = $loanProduct->minimum_amount;
        $max_amount = $loanProduct->maximum_amount;

        $validationRules = [
            'loan_product_id' => 'required|exists:loan_products,id',
            'borrower_id' => 'required',
            'currency_id' => 'required',
            'first_payment_date' => 'required',
            'release_date' => 'required',
            'applied_amount' => "required|numeric|min:$min_amount|max:$max_amount",
            'interest_rate' => 'required|numeric|min:0',
            'late_payment_penalties' => 'required|numeric',
            'attachment' => 'nullable|mimes:jpeg,JPEG,png,PNG,jpg,doc,pdf,docx,zip|max:8192',
            'address' => 'nullable|string',
        ];
        $validationMessages = [];

        // Custom field validation
        $customFields = CustomField::where('table', 'loans')
            ->orderBy('id', 'desc')
            ->get();
        $customValidation = generate_custom_field_validation($customFields);

        array_merge($validationRules, $customValidation['rules']);
        array_merge($validationMessages, $customValidation['messages']);

        $validator = Validator::make($request->all(), $validationRules, $validationMessages);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()
                    ->route('loans.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $attachment = '';
        if ($request->hasfile('attachment')) {
            $file = $request->file('attachment');
            $attachment = time() . $file->getClientOriginalName();
            $file->move(public_path() . '/uploads/media/', $attachment);
        }

        DB::beginTransaction();
        $customFieldsData = store_custom_field_data($customFields);
        $loan = new Loan();
        $loan->loan_product_id = $request->input('loan_product_id');
        $loan->borrower_id = $request->input('borrower_id');
        $loan->currency_id = $request->input('currency_id');
        $loan->first_payment_date = $request->input('first_payment_date');
        $loan->release_date = $request->input('release_date');
        $loan->applied_amount = $request->input('applied_amount');
        $loan->interest_rate = $request->input('interest_rate');
        $loan->late_payment_penalties = $request->input('late_payment_penalties');
        $loan->attachment = $attachment;
        $loan->description = $request->input('description');
        $loan->remarks = $request->input('remarks');
        $loan->created_user_id = Auth::id();
        $loan->branch_id = auth()->user()->branch_id;
        $loan->custom_fields = json_encode($customFieldsData);
        $loan->address = $request->address;
        $loan->save();

        $calculator = new Calculator(
            $loan->applied_amount,
            $loan->first_payment_date,
            $request->input('interest_rate'),
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

        if (!$request->ajax()) {
            return redirect()->route('loans.show', $loan->id)->with('success', _lang('New Loan added successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('New Loan added successfully'), 'data' => $loan, 'table' => '#loans_table']);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $loan = Loan::find($id);
        $loancollaterals = LoanCollateral::where('loan_id', $loan->id)
            ->orderBy('id', 'desc')
            ->get();
        $customFields = CustomField::where('table', 'loans')
            ->where('status', 1)
            ->orderBy('id', 'asc')
            ->get();

        $repayments = LoanRepayment::where('loan_id', $loan->id)->orderBy('id', 'asc')->get();

        $guarantors = Guarantor::where('loan_id', $loan->id)->get();

        $payments = LoanPayment::where('loan_id', $loan->id)->orderBy('id', 'desc')->get();

        // dd($repayments);
        // dd($payments);
        // dd($loan);

        return view('backend.loan.view', compact('loan', 'loancollaterals', 'repayments', 'payments', 'guarantors', 'customFields'));
    }

    /**
     * Approve Loan
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function approve(Request $request, $id)
    // {
    //     DB::beginTransaction();

    //     $loan = Loan::find($id);

    //     if ($loan->status == 1) {
    //         abort(403);
    //     }

    //     if ($loan->loan_id == NULL || $loan->release_date == NULL) {
    //         return back()->with('error', _lang('Loan ID and Release date must required !'));
    //     }

    //     $loan->status = 1;
    //     $loan->approved_date = date('Y-m-d');
    //     $loan->approved_user_id = Auth::id();
    //     $loan->save();

    //     // Create Loan Repayments
    //     $calculator = new Calculator(
    //         $loan->applied_amount,
    //         $loan->getRawOriginal('first_payment_date'),
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

    //     foreach ($repayments as $repayment) {
    //         $loan_repayment = new LoanRepayment();
    //         $loan_repayment->loan_id = $loan->id;
    //         $loan_repayment->repayment_date = $repayment['date'];
    //         $loan_repayment->amount_to_pay = $repayment['amount_to_pay'];
    //         $loan_repayment->penalty = $repayment['penalty'];
    //         $loan_repayment->principal_amount = $repayment['principle_amount'];
    //         $loan_repayment->interest = $repayment['interest'];
    //         $loan_repayment->balance = $repayment['balance'];
    //         $loan_repayment->save();
    //     }

    //     DB::commit();

    //     try {
    //         $loan->borrower->notify(new ApprovedLoanRequest($loan));
    //     } catch (\Exception $e) {
    //     }

    //     return back()->with('success', _lang('Loan Request Approved'));
    // }

    public function approve(Request $request, $id)
    {
        DB::beginTransaction();

        $loan = Loan::find($id);

        if ($loan->status == 1) {
            abort(403);
        }

        if ($loan->loan_id == NULL || $loan->release_date == NULL) {
            return back()->with('error', _lang('Loan ID and Release date must required !'));
        }

        $loan->status = 1;
        $loan->approved_date = date('Y-m-d');
        $loan->approved_user_id = Auth::id();
        $loan->save();

        $calculator = new Calculator(
            $loan->applied_amount,
            $loan->getRawOriginal('first_payment_date'),
            $loan->interest_rate,
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

        foreach ($repayments as $repayment) {
            $loan_repayment = new LoanRepayment();
            $loan_repayment->loan_id = $loan->id;
            $loan_repayment->repayment_date = $repayment['date'];
            $loan_repayment->amount_to_pay = $repayment['amount_to_pay'];
            $loan_repayment->penalty = $repayment['penalty'];
            $loan_repayment->principal_amount = $repayment['principle_amount'];
            $loan_repayment->interest = $repayment['interest'];
            $loan_repayment->balance = $repayment['balance'];
            $loan_repayment->save();
        }

        DB::commit();

        try {
            $loan->borrower->notify(new ApprovedLoanRequest($loan));
        } catch (\Exception $e) {
        }

        $emailResult = app(AgreementEmailService::class)->sendLoanAgreement($loan->fresh());
        if ($emailResult['sent']) {
            return back()->with('success', _lang('Loan Request Approved').'. '.$emailResult['message']);
        }

        return back()->with('error', _lang('Loan approved, but no agreement email was sent.').' '.$emailResult['message']);
    }


    /**
     * Reject Loan
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reject(Request $request, $id)
    {
        $loan = Loan::find($id);
        /** If not pending */
        if ($loan->status != 0) {
            abort(403);
        }
        $loan->status = 3;  // Cancelled
        $loan->save();

        try {
            $loan->borrower->notify(new RejectLoanRequest($loan));
        } catch (\Exception $e) {
        }

        return back()->with('success', _lang('Loan Request Rejected'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function edit(Request $request, $id)
    // {
    //     $loan = Loan::find($id);
    //     if ($loan->status == 2) {
    //         return back()->with('error', _lang('Sorry, This Loan is already completed'));
    //     }

    //     $customFields = CustomField::where('table', 'loans')
    //         ->where('status', 1)
    //         ->orderBy('id', 'asc')
    //         ->get();

    //     if (!$request->ajax()) {
    //         return view('backend.loan.edit', compact('loan', 'id', 'customFields'));
    //     } else {
    //         return view('backend.loan.modal.edit', compact('loan', 'id', 'customFields'));
    //     }
    // }
    public function edit(Request $request, $id)
    {
        $loan = Loan::find($id);
        if ($loan->status == 2) {
            return back()->with('error', _lang('Sorry, This Loan is already completed'));
        }

        $customFields = CustomField::where('table', 'loans')
            ->where('status', 1)
            ->orderBy('id', 'asc')
            ->get();

        $expectedReturns = InvestmentExpectedReturn::where('status', 1)->where('type', 'loan')->get();

        if (!$request->ajax()) {
            return view('backend.loan.edit', compact('loan', 'id', 'customFields', 'expectedReturns'));
        } else {
            return view('backend.loan.modal.edit', compact('loan', 'id', 'customFields', 'expectedReturns'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {
    //     // dd($request->all());
    //     @ini_set('max_execution_time', 0);
    //     @set_time_limit(0);

    //     $loan = Loan::find($id);
    //     if ($loan->status == 2) {
    //         return back()->with('error', _lang('Sorry, This Loan is already completed'));
    //     }
    //     if ($loan->status != 0) {
    //         $loan->description = $request->input('description');
    //         $loan->remarks = $request->input('remarks');

    //         $loan->save();

    //         return redirect()->route('loans.index')->with('success', _lang('Updated successfully'));
    //     } else {
    //         $validationRules = [
    //             'loan_id' => [
    //                 'required',
    //                 Rule::unique('loans')->ignore($id),
    //             ],
    //             'loan_product_id' => 'required',
    //             'borrower_id' => 'required',
    //             'currency_id' => 'required',
    //             'first_payment_date' => 'required',
    //             'release_date' => 'required',
    //             'applied_amount' => 'required|numeric',
    //             'late_payment_penalties' => 'required|numeric',
    //             'attachment' => 'nullable|mimes:jpeg,JPEG,png,PNG,jpg,doc,pdf,docx,zip|max:8192',  // 8MB = 8192KB
    //             'address' => 'nullable|string',
    //         ];

    //         $validationMessages = [];

    //         // Custom field validation
    //         $customFields = CustomField::where('table', 'loans')
    //             ->orderBy('id', 'desc')
    //             ->get();
    //         $customValidation = generate_custom_field_validation($customFields);

    //         array_merge($validationRules, $customValidation['rules']);
    //         array_merge($validationMessages, $customValidation['messages']);

    //         $validator = Validator::make($request->all(), $validationRules, $validationMessages);
    //     }

    //     if ($validator->fails()) {
    //         if ($request->ajax()) {
    //             return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
    //         } else {
    //             return redirect()
    //                 ->route('loans.edit', $id)
    //                 ->withErrors($validator)
    //                 ->withInput();
    //         }
    //     }

    //     if ($request->hasfile('attachment')) {
    //         $file = $request->file('attachment');
    //         $attachment = time() . $file->getClientOriginalName();
    //         $file->move(public_path() . '/uploads/media/', $attachment);
    //     }

    //     DB::beginTransaction();

    //     // Store custom field data
    //     $customFieldsData = store_custom_field_data($customFields);

    //     $loan = Loan::find($id);
    //     $loan->loan_id = $request->input('loan_id');
    //     $loan->loan_product_id = $request->input('loan_product_id');
    //     $loan->borrower_id = $request->input('borrower_id');
    //     $loan->currency_id = $request->input('currency_id');
    //     $loan->first_payment_date = $request->input('first_payment_date');
    //     $loan->release_date = $request->input('release_date');
    //     $loan->applied_amount = $request->input('applied_amount');
    //     $loan->late_payment_penalties = $request->input('late_payment_penalties');
    //     if ($request->hasfile('attachment')) {
    //         $loan->attachment = $attachment;
    //     }
    //     $loan->description = $request->input('description');
    //     $loan->remarks = $request->input('remarks');
    //     $loan->custom_fields = json_encode($customFieldsData);

    //     // Create Loan Repayments
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
    //     $loan->address = $request->input('address');
    //     $loan->save();

    //     DB::commit();

    //     if (!$request->ajax()) {
    //         return redirect()->route('loans.index')->with('success', _lang('Updated successfully'));
    //     } else {
    //         return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated successfully'), 'data' => $loan, 'table' => '#loans_table']);
    //     }
    // }


    public function update(Request $request, $id)
    {
        // dd($request->all());
        @ini_set('max_execution_time', 0);
        @set_time_limit(0);

        $loan = Loan::find($id);
        if ($loan->status == 2) {
            return back()->with('error', _lang('Sorry, This Loan is already completed'));
        }
        if ($loan->status != 0) {
            $loan->description = $request->input('description');
            $loan->remarks = $request->input('remarks');

            $loan->save();

            return redirect()->route('loans.index')->with('success', _lang('Updated successfully'));
        } else {
            $validationRules = [
                'loan_id' => [
                    'required',
                    Rule::unique('loans')->ignore($id),
                ],
                'loan_product_id' => 'required',
                'borrower_id' => 'required',
                'currency_id' => 'required',
                'first_payment_date' => 'required',
                'release_date' => 'required',
                'applied_amount' => 'required|numeric',
                'interest_rate' => 'required|numeric|min:0',  // ब्याज दर के लिए वैलिडेशन जोड़ें
                'late_payment_penalties' => 'required|numeric',
                'attachment' => 'nullable|mimes:jpeg,JPEG,png,PNG,jpg,doc,pdf,docx,zip|max:8192',
                'address' => 'nullable|string',
            ];

            $validationMessages = [];

            // Custom field validation
            $customFields = CustomField::where('table', 'loans')
                ->orderBy('id', 'desc')
                ->get();
            $customValidation = generate_custom_field_validation($customFields);

            array_merge($validationRules, $customValidation['rules']);
            array_merge($validationMessages, $customValidation['messages']);

            $validator = Validator::make($request->all(), $validationRules, $validationMessages);
        }

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()
                    ->route('loans.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        if ($request->hasfile('attachment')) {
            $file = $request->file('attachment');
            $attachment = time() . $file->getClientOriginalName();
            $file->move(public_path() . '/uploads/media/', $attachment);
        }

        DB::beginTransaction();

        // Store custom field data
        $customFieldsData = store_custom_field_data($customFields);

        $loan = Loan::find($id);
        $loan->loan_id = $request->input('loan_id');
        $loan->loan_product_id = $request->input('loan_product_id');
        $loan->borrower_id = $request->input('borrower_id');
        $loan->currency_id = $request->input('currency_id');
        $loan->first_payment_date = $request->input('first_payment_date');
        $loan->release_date = $request->input('release_date');
        $loan->applied_amount = $request->input('applied_amount');
        $loan->interest_rate = $request->input('interest_rate');  // फॉर्म से ब्याज दर लें
        $loan->late_payment_penalties = $request->input('late_payment_penalties');
        if ($request->hasfile('attachment')) {
            $loan->attachment = $attachment;
        }
        $loan->description = $request->input('description');
        $loan->remarks = $request->input('remarks');
        $loan->custom_fields = json_encode($customFieldsData);

        // Create Loan Repayments
        $calculator = new Calculator(
            $loan->applied_amount,
            $loan->first_payment_date,
            $request->input('interest_rate'),  // फॉर्म से ब्याज दर लें
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
        $loan->address = $request->input('address');
        $loan->save();

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('loans.index')->with('success', _lang('Updated successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated successfully'), 'data' => $loan, 'table' => '#loans_table']);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        $loan = Loan::find($id);

        $loancollaterals = LoanCollateral::where('loan_id', $loan->id);
        $loancollaterals->delete();

        $repayments = LoanRepayment::where('loan_id', $loan->id);
        $repayments->delete();

        $loanpayment = LoanPayment::where('loan_id', $loan->id);
        $loanpayment->delete();

        $transaction = Transaction::where('loan_id', $loan->id);
        $transaction->delete();

        $loan->delete();

        DB::commit();

        return redirect()->route('loans.index')->with('success', _lang('Deleted successfully'));
    }

    public function calculator()
    {
        $data = array();
        $data['first_payment_date'] = '';
        $data['apply_amount'] = '';
        $data['interest_rate'] = '';
        $data['interest_type'] = '';
        $data['term'] = '';
        $data['term_period'] = '';
        $data['late_payment_penalties'] = 0;
        return view('backend.loan.calculator', $data);
    }

    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'apply_amount' => 'required|numeric',
            'interest_rate' => 'required',
            'interest_type' => 'required',
            'term' => 'required|integer|max:100',
            'term_period' => $request->interest_type == 'one_time' ? '' : 'required',
            'late_payment_penalties' => 'required',
            'first_payment_date' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('loans.admin_calculator')->withErrors($validator)->withInput();
            }
        }

        $first_payment_date = $request->first_payment_date;
        $apply_amount = $request->apply_amount;
        $interest_rate = $request->interest_rate;
        $interest_type = $request->interest_type;
        $term = $request->term;
        $term_period = $request->term_period;
        $late_payment_penalties = $request->late_payment_penalties;

        $data = array();
        $table_data = array();

        if ($interest_type == 'flat_rate') {
            $calculator = new Calculator($apply_amount, $first_payment_date, $interest_rate, $term, $term_period, $late_payment_penalties);
            $table_data = $calculator->get_flat_rate();
            $data['payable_amount'] = $calculator->payable_amount;
        } else if ($interest_type == 'fixed_rate') {
            $calculator = new Calculator($apply_amount, $first_payment_date, $interest_rate, $term, $term_period, $late_payment_penalties);
            $table_data = $calculator->get_fixed_rate();
            $data['payable_amount'] = $calculator->payable_amount;
        } else if ($interest_type == 'mortgage') {
            $calculator = new Calculator($apply_amount, $first_payment_date, $interest_rate, $term, $term_period, $late_payment_penalties);
            $table_data = $calculator->get_mortgage();
            $data['payable_amount'] = $calculator->payable_amount;
        } else if ($interest_type == 'one_time') {
            $calculator = new Calculator($apply_amount, $first_payment_date, $interest_rate, 1, $term_period, $late_payment_penalties);
            $table_data = $calculator->get_one_time();
            $data['payable_amount'] = $calculator->payable_amount;
        } else if ($interest_type == 'reducing_amount') {
            $calculator = new Calculator($apply_amount, $first_payment_date, $interest_rate, $term, $term_period, $late_payment_penalties);
            $table_data = $calculator->get_reducing_amount();
            $data['payable_amount'] = $calculator->payable_amount;
        }

        $data['table_data'] = $table_data;
        $data['first_payment_date'] = $request->first_payment_date;
        $data['apply_amount'] = $request->apply_amount;
        $data['interest_rate'] = $request->interest_rate;
        $data['interest_type'] = $request->interest_type;
        $data['term'] = $request->term;
        $data['term_period'] = $request->term_period;
        $data['late_payment_penalties'] = $request->late_payment_penalties;

        return view('backend.loan.calculator', $data);
    }

    //  payment
    // public function getDetails($id)
    // {
    //     $repayment = LoanRepayment::findOrFail($id);

    //     $today = Carbon::today();
    //     $due = Carbon::parse($repayment->repayment_date);

    //     $diff = $due->diffInDays($today, false);

    //     $penalty = 0;
    //     if ($diff > 0) {
    //         $penalty = $diff * $repayment->penalty_per_day;
    //     }

    //     // dd(
    //     //     $repayment->id,
    //     //     $repayment->principal_amount,
    //     //     $repayment->interest,
    //     //     $penalty,
    //     //     $repayment->principal_amount + $repayment->interest + $penalty
    //     // );

    //     return response()->json([
    //         'id' => $repayment->id,
    //         'principal_amount' => $repayment->principal_amount,
    //         'interest' => $repayment->interest,
    //         'penalty' => $penalty,
    //         'total' => $repayment->principal_amount + $repayment->interest + $penalty,
    //     ]);
    // }

    public function getDetails($id)
    {
        $repayment = LoanRepayment::with('loan')->findOrFail($id);

        $today = Carbon::today();
        // dd($today);
        // $today = Carbon::parse("2026-03-06");
        // $today = Carbon::parse("2026-04-06");

        // dd($today);
        $dueDate = Carbon::parse($repayment->repayment_date);
        // dd("Today: ".$today, "Due: ".$repayment->repayment_date);

        $penalty = 0;
        $daysLate = 0;

        // Check if today > due date
        if ($today->gt($dueDate)) {
            // dd($today  ,$dueDate);
            // Correct method — always positive, correct days late
            $daysLate = $dueDate->diffInDays($today);
            // dd($daysLate);
            // If penalty per day stored in repayment table
            if ($repayment->penalty_per_day) {
                $penalty = $daysLate * $repayment->penalty_per_day;
            }

            // OR if penalty stored as percentage in loan table
            if ($repayment->loan && $repayment->loan->late_payment_penalties) {
                // $penalty = ($repayment->principal_amount * $repayment->loan->late_payment_penalties / 100) * $daysLate;
                $penalty = ($repayment->amount_to_pay * $repayment->loan->late_payment_penalties / 100) * $daysLate;
            }
        }

        return response()->json([
            'id' => $repayment->id,
            'principal_amount' => $repayment->principal_amount,
            'interest' => $repayment->interest,
            'penalty' => round($penalty, 2),
            // 'penalty_perday'=>round($repayment->penalty_per_day),
            'penalty_perday' => $repayment->loan->late_payment_penalties ?? 0,
            'days_late' => $daysLate,
            'total' => round($repayment->principal_amount + $repayment->interest + $penalty, 2),
        ]);
    }

    // If transaction needed

    public function pay(Request $request)
    {
        //  $total= $repayment->amount_to_pay+$request->late_penalty;

        // dd($request->all(),$repayment->amount_to_pay, $total, );
        $repayment = LoanRepayment::findOrFail($request->emi_id);

        // 1. Update repayment table
        $repayment->penalty = $request->late_penalty;
        $repayment->status = 1;
        $repayment->paid_date = now();
        $repayment->save();

        // dd($repayment->principal_amount , $repayment->amount_to_pay);
        // 2. Insert into loan_payments table
        LoanPayment::create([
            'loan_id' => $repayment->loan_id,
            'paid_at' => now(),
            'late_penalties' => $request->late_penalty ?? 0,
            'interest' => $repayment->interest,
            'repayment_amount' => $repayment->principal_amount,
            'total_amount' => $repayment->amount_to_pay + $request->late_penalty,
            'remarks' => 'EMI Paid',
            'member_id' => $repayment->loan->borrower_id,
            'transaction_id' => null,  // If have transaction ID add here
            'repayment_id' => $repayment->id,
        ]);

        return response()->json([
            'status' => true,
            'msg' => 'EMI Paid Successfully'
        ]);
    }



    /**
     * Show approved loans awaiting disbursement.
     */
    public function disbursementQueue(Request $request)
    {
        $user = Auth::user();

        $loans = Loan::with(['borrower', 'currency', 'loan_product'])
            ->where('status', 1)
            ->whereNull('disbursed_at')
            ->orderByDesc('id');

        if ($user->user_type == 'user') {
            $loans->where('created_user_id', $user->id);
        }

        $loans = $loans->paginate(20);

        return view('backend.loan.disbursement_queue', compact('loans'));
    }

    /**
     * Record a disbursement only after the actual payment is released.
     */
    public function recordDisbursement(Request $request, $id)
    {
        $request->validate([
            'confirm_disbursement' => ['required', 'accepted'],
        ]);

        try {
            DB::transaction(function () use ($id) {
                $loan = Loan::where('status', 1)
                    ->whereNull('disbursed_at')
                    ->whereKey($id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $loan->disbursed_at = now();
                $loan->disbursed_by = Auth::id();
                $loan->save();
            });
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()
                ->route('loans.disbursement_queue')
                ->with('error', _lang('Loan is not awaiting disbursement or is no longer available.'));
        }

        return redirect()
            ->route('loans.disbursement_queue')
            ->with('success', _lang('Disbursement recorded successfully.'));
    }


    /**
     * Overdue Loans — view page
     * Aise loans jinka status = 1 (Active/Approved) hai aur
     * kam se kam ek loan_repayment unpaid (status != 1) aur
     * repayment_date aaj se pehle ki hai.
     */
    public function overdue(Request $request)
    {
        return view('backend.loan.overdue');
    }

    /**
     * Overdue Loans — DataTable AJAX data
     */
    public function get_overdue_table_data(Request $request)
    {
        $user  = auth()->user();
        $today = Carbon::today()->toDateString();

        // Base query: Active loans jinmein unpaid overdue repayments hain
        // withoutGlobalScopes — LoanRepayment ka branch_id global scope bypass karo
        $loans = Loan::select('loans.*')
            ->with(['borrower', 'currency', 'loan_product'])
            ->where('loans.status', 1)
            ->whereHas('repayments', function ($q) use ($today) {
                $q->withoutGlobalScopes()
                  ->where('status', '!=', 1)
                  ->whereDate('repayment_date', '<', $today);
            })
            ->orderBy('loans.id', 'desc');

        // Role-based filter
        if ($user->user_type == 'user') {
            $loans->where('loans.created_user_id', $user->id);
        }

        return Datatables::eloquent($loans)
            ->editColumn('borrower.first_name', function ($loan) {
                return optional($loan->borrower)->first_name . ' ' . optional($loan->borrower)->last_name;
            })
            ->editColumn('applied_amount', function ($loan) {
                return decimalPlace($loan->applied_amount, currency(optional($loan->currency)->name));
            })
            ->addColumn('overdue_amount', function ($loan) use ($today) {
                $overdueAmount = $loan->repayments()
                    ->withoutGlobalScopes()
                    ->where('status', '!=', 1)
                    ->whereDate('repayment_date', '<', $today)
                    ->sum('amount_to_pay');
                return decimalPlace($overdueAmount, currency(optional($loan->currency)->name));
            })
            ->addColumn('overdue_days', function ($loan) use ($today) {
                $oldest = $loan->repayments()
                    ->withoutGlobalScopes()
                    ->where('status', '!=', 1)
                    ->whereDate('repayment_date', '<', $today)
                    ->orderBy('repayment_date', 'asc')
                    ->first();
                if ($oldest) {
                    $days = Carbon::parse($oldest->getRawOriginal('repayment_date'))->diffInDays(Carbon::today());
                    return '<span class="badge badge-danger">' . $days . ' ' . _lang('days') . '</span>';
                }
                return '-';
            })
            ->editColumn('status', function ($loan) {
                return show_status(_lang('Overdue'), 'danger');
            })
            ->filterColumn('borrower.first_name', function ($query, $keyword) {
                $query->whereHas('borrower', function ($query) use ($keyword) {
                    return $query
                        ->where('first_name', 'like', "{$keyword}%")
                        ->orWhere('last_name', 'like', "{$keyword}%");
                });
            }, true)
            ->addColumn('action', function ($loan) {
                $action = '<div class="text-center">';
                $action .= '<a href="' . route('loans.show', $loan['id']) . '" class="btn btn-primary btn-xs"><i class="ti-eye"></i> ' . _lang('View') . '</a>';
                $action .= '</div>';
                return $action;
            })
            ->setRowId(function ($loan) {
                return 'row_' . $loan->id;
            })
            ->rawColumns(['status', 'action', 'overdue_days'])
            ->make(true);
    }
}
