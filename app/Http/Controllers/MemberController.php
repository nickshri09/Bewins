<?php

namespace App\Http\Controllers;

use App\Imports\MembersImport;
use App\Mail\GeneralMail;
use App\Models\CustomField;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\MemberRequestAccepted;
use App\Utilities\Overrider;
use App\Utilities\SmsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use DataTables;
use App\Models\MemberWallet;
use App\Models\MemberWalletTransaction;

class MemberController extends Controller
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
    public function index()
    {
        // dd('safsaf');

        return view('backend.member.list');
    }

    public function get_table_data()
    {
        // $members = Member::select('members.*')
        //     ->with('branch')
        //     ->orderBy("members.id", "desc");

        $user = auth()->user();

        $members = Member::query()
            // Assigned clients can belong to a different branch than the user
            // they were reassigned to, so ownership is the relevant filter here.
            ->when($user->user_type !== 'admin', function ($query) {
                $query->withoutGlobalScope('branch_id');
            })
            ->leftJoin('member_wallets', 'members.id', '=', 'member_wallets.member_id')
            ->select('members.*', 'member_wallets.balance as wallet_balance')
            ->with('branch')
            ->orderBy('members.id', 'desc')
            ->when($user->user_type !== 'admin', function ($query) use ($user) {
                $query->where('members.assigned_user_id', $user->id);
            });

        return Datatables::eloquent($members)
            ->editColumn('branch.name', function ($member) {
                return $member->branch->name ?? '';
            })
            ->editColumn('photo', function ($member) {
                $photo = $member->photo != null ? profile_picture($member->photo) : asset('public/backend/images/avatar.png');
                return '<div class="profile_picture text-center">'
                    . '<img src="' . $photo . '" class="thumb-sm img-thumbnail">'
                    . '</div>';
            })
            ->addColumn('action', function ($member) {
                $action = '<div class="dropdown text-center">'
                    . '<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                    . '&nbsp;</button>'
                    . '<div class="dropdown-menu">'
                    . '<a class="dropdown-item" href="' . route('members.edit', $member->id) . '"><i class="ti-pencil-alt"></i> ' . _lang('Edit') . '</a>'
                    . '<a class="dropdown-item" href="' . route('members.show', $member->id) . '"><i class="ti-eye"></i> ' . _lang('View') . '</a>'
                    . '<a class="dropdown-item" href="' . route('member_documents.index', $member->id) . '"><i class="ti-files"></i> ' . _lang('Documents') . '</a>';

                // 👇 Delete button only for Admins
                if (auth()->user()->user_type == 'admin') {
                    $action .= '<form action="' . route('members.destroy', $member->id) . '" method="post">'
                        . csrf_field()
                        . '<input name="_method" type="hidden" value="DELETE">'
                        . '<button class="dropdown-item btn-remove" type="submit"><i class="ti-trash"></i> ' . _lang('Delete') . '</button>'
                        . '</form>';
                }

                $action .= '</div></div>';
                return $action;
            })
            ->setRowId(function ($member) {
                return 'row_' . $member->id;
            })
            ->addColumn('allow_withdrawal', function ($member) {
                $checked = $member->allow_withdrawal ? 'checked' : '';
                return '<div class="custom-control custom-switch text-center">
                            <input type="checkbox" class="custom-control-input toggle-withdrawal" data-id="'.$member->id.'" id="customSwitch'.$member->id.'" '.$checked.'>
                            <label class="custom-control-label" for="customSwitch'.$member->id.'"></label>
                        </div>';
            })
            ->rawColumns(['photo', 'action', 'allow_withdrawal'])
            ->make(true);
    }

    public function pending_requests()
    {
        $data = array();
        $data['members'] = Member::where('status', 0)
            ->withoutGlobalScopes(['status'])
            ->paginate(10);
        return view('backend.member.pending_requests', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $customFields = CustomField::where('table', 'members')
            ->where('status', 1)
            ->orderBy('id', 'asc')
            ->get();

        $memberNo = get_option('starting_member_no');

        // Check if member already exists, if so increment until unique
        while (\App\Models\Member::where('member_no', $memberNo)->exists()) {
            $memberNo++;
            update_option('starting_member_no', $memberNo);
        }

        return view('backend.member.create', compact('customFields', 'memberNo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        // dd($request->all() ,$user->id);
        $validationRules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'nullable|email|unique:members|max:191',
            'member_no' => 'required|unique:members|max:50',
            'referrer_code' => ['nullable', 'string', function ($attribute, $value, $fail) {
                $code = strtoupper(trim($value));
                $isClientCode = Member::withoutGlobalScopes()->whereRaw('UPPER(referral_code) = ?', [$code])->exists();
                $isUserCode = User::whereRaw('UPPER(referral_code) = ?', [$code])
                    ->where('user_type', 'user')->where('status', 1)->exists();
                if (!$isClientCode && !$isUserCode) {
                    $fail(_lang('The referral code must belong to an active client or office user.'));
                }
            }],
            'country_code' => 'required_with:mobile',
            'photo' => 'nullable|image',
            // User Login Attributes
            'name' => 'required_if:client_login,1|max:191',
            'login_email' => 'required_if:client_login,1|email|unique:users,email|max:191',
            'password' => 'required_if:client_login,1|max:20|min:6',
            'status' => 'required_if:client_login,1',
            'pancard_no' => 'nullable|string|max:20',
            'aadhaar_no' => 'nullable|string|max:20',
            'pancard_image' => 'nullable|image',
            'aadhaar_front' => 'nullable|image',
            'aadhaar_back' => 'nullable|image',
        ];

        $validationMessages = [
            'name.required_if' => 'Name is required',
            'login_email.required_if' => 'Email is required',
            'password.required_if' => 'Password is required',
            'country_code.required_with' => 'Country code is required',
        ];

        // Custom field validation
        $customFields = CustomField::where('table', 'members')
            ->orderBy('id', 'desc')
            ->get();
        $customValidation = generate_custom_field_validation($customFields);

        array_merge($validationRules, $customValidation['rules']);
        array_merge($validationMessages, $customValidation['messages']);

        $validator = Validator::make($request->all(), $validationRules, $validationMessages);

        if ($validator->fails()) {
            if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            }
            return redirect()
                ->route('members.create')
                ->withErrors($validator)
                ->withInput();
        }

        $photo = 'default.png';
        if ($request->hasfile('photo')) {
            $file = $request->file('photo');
            $photo = time() . $file->getClientOriginalName();
            $file->move(public_path() . '/uploads/profile/', $photo);
        }

        // Handle PAN card image upload
        $pancard_image = null;
        if ($request->hasFile('pancard_image')) {
            $file = $request->file('pancard_image');
            $pancard_image = time() . '_pan_' . $file->getClientOriginalName();
            $file->move(public_path('/uploads/documents/'), $pancard_image);
        }

        // Handle Aadhaar front image upload
        $aadhaar_front = null;
        if ($request->hasFile('aadhaar_front')) {
            $file = $request->file('aadhaar_front');
            $aadhaar_front = time() . '_aadhaar_front_' . $file->getClientOriginalName();
            $file->move(public_path('/uploads/documents/'), $aadhaar_front);
        }

        // Handle Aadhaar back image upload
        $aadhaar_back = null;
        if ($request->hasFile('aadhaar_back')) {
            $file = $request->file('aadhaar_back');
            $aadhaar_back = time() . '_aadhaar_back_' . $file->getClientOriginalName();
            $file->move(public_path('/uploads/documents/'), $aadhaar_back);
        }

        DB::beginTransaction();

        // Store custom field data
        $customFieldsData = store_custom_field_data($customFields);

        // Create Login details
        if ($request->client_login == 1) {
            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('login_email');
            $user->password = Hash::make($request->password);
            $user->user_type = 'customer';
            $user->status = $request->input('status');
            $user->profile_picture = $photo;
            $user->save();
        }

        // dd(Auth::user()->id);

        $member = new Member();
        $member->first_name = $request->input('first_name');
        $member->last_name = $request->input('last_name');
        if ($request->filled('referrer_code')) {
            $code = strtoupper(trim($request->input('referrer_code')));
            $referringMember = Member::withoutGlobalScopes()->whereRaw('UPPER(referral_code) = ?', [$code])->first();
            $member->referred_by_member_id = $referringMember?->id;
            $member->referred_by_user_id = $referringMember ? null : User::whereRaw('UPPER(referral_code) = ?', [$code])
                ->where('user_type', 'user')->where('status', 1)->value('id');
        }
        if (auth()->user()->user_type == 'admin') {
            $member->branch_id = $request->branch_id;
        } else {
            $member->branch_id = auth()->user()->branch_id;
        }
        if ($request->client_login == 1) {
            $member->user_id = $user->id;
        }
        $member->email = $request->input('email');
        $member->country_code = $request->input('country_code');
        $member->mobile = $request->input('mobile');
        $member->business_name = $request->input('business_name');
        $member->member_no = get_option('starting_member_no', $request->input('member_no'));
        $member->gender = $request->input('gender');
        $member->city = $request->input('city');
        $member->state = $request->input('state');
        $member->zip = $request->input('zip');
        $member->address = $request->input('address');
        $member->credit_source = $request->input('credit_source');
        $member->photo = $photo;
        $member->custom_fields = json_encode($customFieldsData);

        $member->pancard_no = $request->input('pancard_no');
        $member->aadhaar_no = $request->input('aadhaar_no');
        $member->pancard_image = $pancard_image;
        $member->aadhaar_front = $aadhaar_front;
        $member->aadhaar_back = $aadhaar_back;
        $member->created_by = Auth::user()->id;
        $member->assigned_user_id = Auth::id();

        $member->save();

        // Increment Member No
        $memberNo = get_option('starting_member_no');
        if ($memberNo != '') {
            update_option('starting_member_no', $memberNo + 1);
        }

        DB::commit();

        if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $member, 'table' => '#members_table']);
        }
        return redirect()->route('members.create')->with('success', _lang('Saved Successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // dd($request->all());
        $member = Member::withoutGlobalScopes(['status'])->find($id);
        $customFields = CustomField::where('table', 'members')
            ->where('status', 1)
            ->orderBy('id', 'asc')
            ->get();

        // dd($member,$customFields);
        if (!$request->ajax()) {
            return view('backend.member.view', compact('member', 'id', 'customFields'));
        } else {
            return view('backend.member.modal.view', compact('member', 'id', 'customFields'));
        }
    }

    public function get_member_transaction_data($member_id)
    {
        $transactions = Transaction::select('transactions.*')
            ->with(['member', 'account', 'account.savings_type'])
            ->where('member_id', $member_id)
            ->orderBy('transactions.trans_date', 'desc');

        return Datatables::eloquent($transactions)
            ->editColumn('member.first_name', function ($transactions) {
                return $transactions->member->first_name . ' ' . $transactions->member->last_name;
            })
            ->editColumn('dr_cr', function ($transactions) {
                return strtoupper($transactions->dr_cr);
            })
            ->editColumn('status', function ($transactions) {
                return transaction_status($transactions->status);
            })
            ->editColumn('amount', function ($transaction) {
                $symbol = $transaction->dr_cr == 'dr' ? '-' : '+';
                $class = $transaction->dr_cr == 'dr' ? 'text-danger' : 'text-success';
                return '<span class="' . $class . '">' . $symbol . ' ' . decimalPlace($transaction->amount, currency($transaction->account->savings_type->currency->name)) . '</span>';
            })
            ->editColumn('type', function ($transaction) {
                return str_replace('_', ' ', $transaction->type);
            })
            ->filterColumn('member.first_name', function ($query, $keyword) {
                $query->whereHas('member', function ($query) use ($keyword) {
                    return $query
                        ->where('first_name', 'like', "{$keyword}%")
                        ->orWhere('last_name', 'like', "{$keyword}%");
                });
            }, true)
            ->addColumn('action', function ($transaction) {
                return '<div class="dropdown text-center">'
                    . '<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                    . '&nbsp;</button>'
                    . '<div class="dropdown-menu">'
                    . '<a class="dropdown-item" href="' . route('transactions.edit', $transaction['id']) . '"><i class="ti-pencil-alt"></i> ' . _lang('Edit') . '</a>'
                    . '<a class="dropdown-item" href="' . route('transactions.show', $transaction['id']) . '"><i class="ti-eye"></i>  ' . _lang('View') . '</a>'
                    . '<form action="' . route('transactions.destroy', $transaction['id']) . '" method="post">'
                    . csrf_field()
                    . '<input name="_method" type="hidden" value="DELETE">'
                    . '<button class="dropdown-item btn-remove" type="submit"><i class="ti-trash"></i> ' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            })
            ->setRowId(function ($transaction) {
                return 'row_' . $transaction->id;
            })
            ->rawColumns(['action', 'status', 'amount'])
            ->make(true);
    }

    public function get_wallet_transaction_data($member_id)
    {
        $transactions = \App\Models\MemberWalletTransaction::where('member_id', $member_id)
            ->orderBy('id', 'desc');

        return Datatables::eloquent($transactions)
            ->editColumn('created_at', function ($transaction) {
                return $transaction->created_at->format('d M, Y H:i:s');
            })
            ->editColumn('type', function ($transaction) {
                return strtoupper($transaction->type);
            })
            ->editColumn('amount', function ($transaction) {
                $symbol = $transaction->type == 'debit' ? '-' : '+';
                $class = $transaction->type == 'debit' ? 'text-danger' : 'text-success';
                return '<span class="' . $class . '">' . $symbol . ' ' . decimalPlace($transaction->amount) . '</span>';
            })
            ->addColumn('period', function ($transaction) {
                if ($transaction->posted_from && $transaction->posted_to) {
                    return $transaction->posted_from . ' to ' . $transaction->posted_to;
                }
                return '-';
            })
            ->rawColumns(['amount'])
            ->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $customFields = CustomField::where('table', 'members')
            ->where('status', 1)
            ->orderBy('id', 'asc')
            ->get();
        $member = Member::withoutGlobalScopes(['status'])->find($id);
        $memberCommissions = \App\Models\CommissionRecord::with(['investment', 'sourceMember'])
            ->where('recipient_type', 'member')
            ->where('recipient_id', $member->id)
            ->latest()
            ->take(10)
            ->get();
        $memberCommissionTotal = (float) \App\Models\CommissionRecord::where('recipient_type', 'member')
            ->where('recipient_id', $member->id)
            ->sum('amount');
        $assignees = collect();

        if (auth()->user()->user_type === 'admin') {
            $assignees = User::whereIn('user_type', ['admin', 'user'])
                ->where('status', 1)
                ->orderBy('name')
                ->get(['id', 'name', 'email']);
        }

        if (!$request->ajax()) {
            return view('backend.member.edit', compact('member', 'id', 'customFields', 'assignees', 'memberCommissions', 'memberCommissionTotal'));
        } else {
            return view('backend.member.modal.edit', compact('member', 'id', 'customFields', 'assignees', 'memberCommissions', 'memberCommissionTotal'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $member = Member::withoutGlobalScopes(['status'])->find($id);
        $existingLoginUser = $member->user_id ? User::find($member->user_id) : null;

        $validationRules = [
            'first_name' => 'required',
            'last_name' => 'required',
            // 'branch_id'    => 'required',
            'email' => [
                'nullable',
                'email',
                Rule::unique('members')->ignore($id),
            ],
            'member_no' => [
                'required',
                Rule::unique('members')->ignore($id),
            ],
            'referrer_code' => ['nullable', 'string', function ($attribute, $value, $fail) {
                $code = strtoupper(trim($value));
                $isClientCode = Member::withoutGlobalScopes()->whereRaw('UPPER(referral_code) = ?', [$code])->exists();
                $isUserCode = User::whereRaw('UPPER(referral_code) = ?', [$code])
                    ->where('user_type', 'user')->where('status', 1)->exists();
                if (!$isClientCode && !$isUserCode) {
                    $fail(_lang('The referral code must belong to an active client or office user.'));
                }
            }],
            'country_code' => 'required_with:mobile',
            'photo' => 'nullable|image',
            'name' => 'required_if:client_login,1|max:191',  // User Login Attribute
            'login_email' => [
                'required_if:client_login,1',
                Rule::unique('users', 'email')->ignore($member->user_id),
            ],  // User Login Attribute
            'password' => 'nullable|string|max:20|min:6',  // User Login Attribute
            'status' => 'required_if:client_login,1',  // User Login Attribute
        ];

        // A client without a linked login (including an orphaned user_id) needs
        // a password when login is being enabled for the first time.
        if ($request->input('client_login') == 1 && !$existingLoginUser) {
            $validationRules['password'] = 'required|string|max:20|min:6';
        }

        $isAdmin = auth()->user()->user_type === 'admin';
        if ($isAdmin) {
            $validationRules['assigned_user_id'] = [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->whereIn('user_type', ['admin', 'user'])->where('status', 1);
                }),
            ];
        }

        $validationMessages = [
            'name.required_if' => 'Name is required',
            'login_email.required_if' => 'Email is required',
            'password.required_if' => 'Password is required',
            'country_code.required_with' => 'Country code is required',
        ];

        // Custom field validation
        $customFields = CustomField::where('table', 'members')
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
                    ->route('members.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        if ($request->filled('referrer_code')) {
            $code = strtoupper(trim($request->input('referrer_code')));
            $referrer = Member::withoutGlobalScopes()->whereRaw('UPPER(referral_code) = ?', [$code])->first();
            $cursor = $referrer;
            $visitedReferrers = [];
            while ($cursor && !in_array((int) $cursor->id, $visitedReferrers, true)) {
                if ((int) $cursor->id === (int) $member->id) {
                    return redirect()->route('members.edit', $id)
                        ->withErrors(['referrer_code' => _lang('This referral would create a circular referral chain.')])
                        ->withInput();
                }
                $visitedReferrers[] = (int) $cursor->id;
                $cursor = $cursor->referrer;
            }
        }

        if ($request->hasfile('photo')) {
            $file = $request->file('photo');
            $photo = time() . $file->getClientOriginalName();
            $file->move(public_path() . '/uploads/profile/', $photo);
        }

        if ($request->hasFile('pancard_image')) {
            $file = $request->file('pancard_image');
            $pancardImage = time() . '_pan.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/pancards'), $pancardImage);
            $member->pancard_image = $pancardImage;
        }

        if ($request->hasFile('aadhaar_front')) {
            $file = $request->file('aadhaar_front');
            $aadhaarFront = time() . '_front.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/aadhaar'), $aadhaarFront);
            $member->aadhaar_front = $aadhaarFront;
        }

        if ($request->hasFile('aadhaar_back')) {
            $file = $request->file('aadhaar_back');
            $aadhaarBack = time() . '_back.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/aadhaar'), $aadhaarBack);
            $member->aadhaar_back = $aadhaarBack;
        }

        DB::beginTransaction();

        // Store custom field data
        $customFieldsData = store_custom_field_data($customFields);

        //         if ($request->client_login == 1) {
        //             if ($member->user_id != NULL) {
        //                 $user = User::find($member->user_id);
        //             } else {
        //                 $user = new User();
        //             }
        //             $user->name   = $request->input('name');
        //             $user->email  = $request->input('login_email');
        //             $user->status = $request->input('status');
        //             if ($request->password) {
        //                 $user->password = Hash::make($request->password);
        //             }
        //             //  $user->user_type = 'customer';
        //              $user->user_type = 'user';
        // //dd($user);
        //             $user->save();
        //         }

        if ($request->client_login == 1) {
            $user = $existingLoginUser;
            if (!$user) {
                $user = new User();
                $user->user_type = 'customer';
            }

            $user->name = $request->input('name');
            $user->email = $request->input('login_email');
            $user->status = $request->input('status');

            if ($request->password) {
                $user->password = Hash::make($request->password);
            }

            $user->save();
        }

        $member->first_name = $request->input('first_name');
        $member->last_name = $request->input('last_name');
        if ($request->has('referrer_code')) {
            $code = strtoupper(trim($request->input('referrer_code', '')));
            $referringMember = $code
                ? Member::withoutGlobalScopes()->whereRaw('UPPER(referral_code) = ?', [$code])->first()
                : null;
            $member->referred_by_member_id = $referringMember?->id;
            $member->referred_by_user_id = $code && !$referringMember
                ? User::whereRaw('UPPER(referral_code) = ?', [$code])->where('user_type', 'user')->where('status', 1)->value('id')
                : null;
        }
        if (auth()->user()->user_type == 'admin') {
            $member->branch_id = $request->branch_id;
            if ($request->has('assigned_user_id')) {
                $member->assigned_user_id = $request->filled('assigned_user_id')
                    ? $request->input('assigned_user_id')
                    : null;
            }
        } else {
            $member->branch_id = auth()->user()->branch_id;
        }
        if ($request->client_login == 1) {
            $member->user_id = $user->id;
        }
        $member->email = $request->input('email');
        $member->country_code = $request->input('country_code');
        $member->mobile = $request->input('mobile');
        $member->business_name = $request->input('business_name');
        $member->member_no = $request->input('member_no');
        $member->gender = $request->input('gender');
        $member->city = $request->input('city');
        $member->state = $request->input('state');
        $member->zip = $request->input('zip');
        $member->address = $request->input('address');
        $member->credit_source = $request->input('credit_source');

        $member->pancard_no = $request->input('pan_card');
        $member->aadhaar_no = $request->input('aadhaar_no');
        // $member->pancard_image  = $pancard_image;
        // $member->aadhaar_front  = $aadhaar_front;
        // $member->aadhaar_back   = $aadhaar_back;

        if ($request->hasfile('photo')) {
            $member->photo = $photo;
        }
        $member->custom_fields = json_encode($customFieldsData);

        $member->save();

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('members.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $member, 'table' => '#members_table']);
        }
    }

    public function send_email(Request $request)
    {
        @ini_set('max_execution_time', 0);
        @set_time_limit(0);

        Overrider::load('Settings');

        $validator = Validator::make($request->all(), [
            'user_email' => 'required',
            'subject' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        // Send email
        $subject = $request->input('subject');
        $message = $request->input('message');

        $mail = new \stdClass();
        $mail->subject = $subject;
        $mail->body = $message;

        try {
            Mail::to($request->user_email)->send(new GeneralMail($mail));
        } catch (\Exception $e) {
            if (!$request->ajax()) {
                return back()->with('error', _lang('Sorry, Error Occured !'));
            } else {
                return response()->json(['result' => 'error', 'message' => _lang('Sorry, Error Occured !')]);
            }
        }

        if (!$request->ajax()) {
            return back()->with('success', _lang('Email Send Sucessfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Email Send Sucessfully'), 'data' => $contact]);
        }
    }

    public function send_sms(Request $request)
    {
        @ini_set('max_execution_time', 0);
        @set_time_limit(0);

        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'message' => 'required:max:160',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        // Send message
        $message = $request->input('message');

        if (get_option('sms_gateway') == 'none') {
            return back()->with('error', _lang('Sorry, SMS Gateway is disabled !'));
        }

        try {
            $sms = new SmsHelper();
            $sms->send($request->phone, $message);
        } catch (\Exception $e) {
            if (!$request->ajax()) {
                return back()->with('error', _lang('Sorry, Error Occured !'));
            } else {
                return response()->json(['result' => 'error', 'message' => _lang('Sorry, Error Occured !')]);
            }
        }

        if (!$request->ajax()) {
            return back()->with('success', _lang('SMS Send Sucessfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('SMS Send Sucessfully'), 'data' => $contact]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // Allow only admin to delete
        if (!auth()->check() || auth()->user()->user_type !== 'admin') {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => _lang('Permission denied')], 403);
            }
            return redirect()->route('members.index')->with('error', _lang('Permission denied'));
        }

        $member = Member::find($id);
        if (!$member) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => _lang('Member not found')], 404);
            }
            return redirect()->route('members.index')->with('error', _lang('Member not found'));
        }

        DB::beginTransaction();
        try {
            // if ($member->user && in_array($member->user->user_type, ['customer', 'user'])) {
            //     $member->user->delete();
            // }
            $member->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => _lang('Sorry, Error Occured !')], 500);
            }
            return redirect()->route('members.index')->with('error', _lang('Sorry, Error Occured !'));
        }

        if ($request->ajax()) {
            return response()->json(['result' => 'success', 'message' => _lang('Deleted Successfully')]);
        }
        return redirect()->route('members.index')->with('success', _lang('Deleted Successfully'));
    }

    public function accept_request(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $member = Member::withoutGlobalScopes(['status'])->find($id);
            return view('backend.member.modal.accept_request', compact('member'));
        } else {
            $validator = Validator::make($request->all(), [
                'member_no' => [
                    'required',
                    Rule::unique('members')->ignore($id),
                ],
            ]);

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
                } else {
                    return back()->withErrors($validator)->withInput();
                }
            }

            DB::beginTransaction();

            $member = Member::withoutGlobalScopes(['status'])->find($id);
            $member->member_no = $request->member_no;
            $member->status = 1;
            $member->save();

            $member->user->status = 1;
            $member->user->save();

            DB::commit();

            if ($member->status == 1) {
                try {
                    $member->notify(new MemberRequestAccepted($member));
                } catch (\Exception $e) {
                }
            }

            if (!$request->ajax()) {
                return redirect()->route('members.index')->with('success', _lang('Member Request Accepted'));
            } else {
                return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Member Request Accepted'), 'data' => $member, 'table' => '#members_table']);
            }
        }
    }

    public function reject_request($id)
    {
        $member = Member::withoutGlobalScopes(['status'])->find($id);
        if ($member->user && in_array($member->user->user_type, ['customer', 'user'])) {
            $member->user->delete();
        }
        $member->delete();
        return redirect()->back()->with('error', _lang('Member Request Rejected'));
    }

    public function import(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('backend.member.import');
        } else if ($request->isMethod('post')) {
            @ini_set('max_execution_time', 0);
            @set_time_limit(0);

            $validator = Validator::make($request->all(), [
                'file' => 'required|mimes:xlsx',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $new_rows = 0;

            DB::beginTransaction();

            $previous_rows = Member::count();

            $data = array();
            $import = Excel::import(new MembersImport($data), $request->file('file'));

            $current_rows = Member::count();

            $new_rows = $current_rows - $previous_rows;

            DB::commit();

            if ($new_rows == 0) {
                return back()->with('error', _lang('Nothing Imported, Data may already exists !'));
            }
            return back()->with('success', $new_rows . ' ' . _lang('Rows Imported Sucessfully'));
        }
    }

    public function toggle_withdrawal(Request $request, $id)
    {
        if (!auth()->check() || auth()->user()->user_type !== 'admin') {
            return response()->json(['result' => 'error', 'message' => _lang('Permission denied')], 403);
        }

        $validated = $request->validate([
            'status' => 'required|boolean',
        ]);

        $member = Member::find($id);
        if (!$member) {
            return response()->json(['result' => 'error', 'message' => _lang('Member not found')], 404);
        }

        $member->allow_withdrawal = (bool) $validated['status'];
        $member->save();

        $statusText = $member->allow_withdrawal ? _lang('enabled') : _lang('disabled');

        return response()->json([
            'result' => 'success',
            'message' => _lang('Withdrawal ' . $statusText . ' successfully'),
            'allow_withdrawal' => (bool) $member->allow_withdrawal,
        ]);
    }
    public function add_wallet_money(Request $request, $member_id)
    {
        $member = Member::find($member_id);
        if (!$request->ajax()) {
            return view('backend.member.add_wallet_money', compact('member'));
        } else {
            return view('backend.member.modal.add_wallet_money', compact('member'));
        }
    }

    public function store_wallet_money(Request $request, $member_id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:manual,razorpay',
            'note'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return back()->withErrors($validator)->withInput();
            }
        }

        $member = Member::find($member_id);
        $wallet = MemberWallet::firstOrCreate(['member_id' => $member->id], ['balance' => 0]);

        if ($request->method == 'manual') {
            DB::beginTransaction();
            try {
                $wallet->balance += $request->amount;
                $wallet->save();

                MemberWalletTransaction::create([
                    'member_wallet_id' => $wallet->id,
                    'member_id'        => $member->id,
                    'type'             => 'credit',
                    'amount'           => $request->amount,
                    'gross_amount'     => $request->amount,
                    'note'             => $request->note ?? 'Manual credit by admin',
                    'created_by'       => Auth::id(),
                ]);

                DB::commit();

                if ($request->ajax()) {
                    return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Money added successfully'), 'table' => '#wallet_transactions_table']);
                } else {
                    return back()->with('success', _lang('Money added successfully'));
                }
            } catch (\Exception $e) {
                DB::rollBack();
                if ($request->ajax()) {
                    return response()->json(['result' => 'error', 'message' => _lang('Sorry, Error Occured !')]);
                } else {
                    return back()->with('error', _lang('Sorry, Error Occured !'));
                }
            }
        } else {
            return response()->json([
                'result' => 'success', 
                'action' => 'razorpay', 
                'amount' => $request->amount,
                'note'   => $request->note,
                'member_id' => $member->id,
                'razorpay_key' => get_option('razorpay_key_id', env('RAZORPAY_KEY'))
            ]);
        }
    }
}
