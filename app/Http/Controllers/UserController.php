<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct() {
        date_default_timezone_set(get_option('timezone', 'Asia/Dhaka'));
    }

   

public function index(Request $request)
{
    // $query = User::query()->orderBy('id', 'desc');
     $query = User::query()
        ->whereNotIn('email', ['admin@gmail.com', 'bellway@gmail.com'])
        // Client accounts belong in Client Management, even when a legacy
        // account was incorrectly assigned an office user_type.
        ->whereNotIn('email', DB::table('members')->whereNotNull('email')->select('email'))
        ->where(function ($query) {
            $query->whereNull('user_type')
                ->orWhereRaw('LOWER(user_type) != ?', ['customer']);
        })
        ->orderBy('id', 'desc');

    if ($request->branch_id) {
        $query->where('branch_id', $request->branch_id);
    }

    if ($request->search) {
        $query->where(function($q) use ($request) {
            $q->where('name', 'like', "%{$request->search}%")
              ->orWhere('email', 'like', "%{$request->search}%")
              ->orWhere('mobile_number', 'like', "%{$request->search}%")
              ->orWhere('referral_code', 'like', "%{$request->search}%");
        });
    }

    // 🔥 Pagination added
    // $users = $query->paginate(10);
     $users = $query->get();

    $branches = DB::table('branches')->orderBy('name')->get();

    // AJAX request - return only table rows + pagination
    if ($request->ajax()) {
        return view('backend.user.list', compact('users', 'branches'))->render();
    }

    return view('backend.user.list', compact('users', 'branches'));
}



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create(Request $request)
{
    if (!$request->ajax()) {
        // Get list of managers
        // $managers = User::whereHas('role', function ($query) {
        //     $query->where('name', 'manager');
        // })->get();
        $managers = User::where('role_id', 31)->get();

        return view('backend.user.create', compact('managers'));
    } else {
        return back();
    }
}


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    //manish
     public function store(Request $request)
    {
        // dd($request->all());
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'mobile_number' => 'required|unique:users|max:255',
            'whatsapp_number' => 'nullable|string|max:20',
            // 'aadhar_photo.*'       => 'nullable|mimes:jpg,jpeg,png,avif,pdf|max:2048',
            'aadhar_number' => ['nullable', 'regex:/^\d{4}\s?\d{4}\s?\d{4}$/'],
            'pan_photo' => 'nullable|mimes:jpg,jpeg,png,avif,pdf|max:2048',
            // 'pan_number'           => 'nullable|string|max:10',
            'pan_number' => ['nullable', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'],
            'gst_type' => 'nullable|in:Regular,Composition',
            'gst_number' => 'nullable|string|max:20',
            'residence_address' => 'nullable|string|max:1000',
            'office_address' => 'nullable|string|max:1000',
            'account_type' => 'nullable|in:Savings,Current',
            'account_name' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:20',
            'account_number' => 'nullable|string|max:50',
            'user_type' => 'required',
            'status' => 'required',
            'profile_picture' => 'nullable|image',
            'password' => 'required|min:6',
            'assigned_manager_id' => 'required_if:role_id,agent',
            'documents.*' => 'nullable|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
            'percent' => 'nullable|numeric|min:0|max:100',
            'aadhaar_front' => 'nullable|image',
            'aadhaar_back' => 'nullable|image',
            'designation' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric',
            // 'user_id' removed since auto-generated
        ], [
            'aadhar_number.regex' => 'Enter a valid 12-digit Aadhaar number (e.g. 1234 5678 9123)',
            'pan_number.regex' => 'Enter a valid PAN number in format ABCDE1234F',
        ]);
        $messages = [
            'aadhaar_number.regex' => 'Enter a valid 12-digit Aadhaar number (e.g. 1234 5678 9123)',
            'pan_number.regex' => 'Enter a valid PAN number in format ABCDE1234F',
        ];

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()
                    ->route('users.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        // Handle profile picture
        $profile_picture = 'default.png';
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $profile_picture = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/profile'), $profile_picture);
        }

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

        // Handle PAN photo
        $pan_photo_filename = null;
        if ($request->hasFile('pan_photo')) {
            $pan_photo = $request->file('pan_photo');
            $pan_photo_filename = time() . '_' . $pan_photo->getClientOriginalName();
            $pan_photo->move(public_path('uploads/documents'), $pan_photo_filename);
        }

        // Save user
        $user = new User();
        $user->name = $request->input('name');
        $user->middle_name = $request->input('middle_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->mobile_number = $request->input('mobile_number');
        $user->whatsapp_number = $request->input('whatsapp_number');
        // $user->aadhar_photo       = !empty($aadhar_photos) ? json_encode($aadhar_photos) : null;
        $user->aadhar_number = $request->input('aadhar_number');
        $user->pan_photo = $pan_photo_filename;
        $user->pan_number = $request->input('pan_number');
        $user->gst_type = $request->input('gst_type');
        $user->gst_number = $request->input('gst_number');
        $user->residence_address = $request->input('residence_address');
        $user->office_address = $request->input('office_address');
        $user->account_type = $request->input('account_type');
        $user->account_name = $request->input('account_name');
        $user->bank_name = $request->input('bank_name');
        $user->ifsc_code = $request->input('ifsc_code');
        $user->account_number = $request->input('account_number');
        $user->user_type = $request->input('user_type');
        $user->role_id = $request->input('role_id');
        $user->branch_id = $request->input('branch_id');
        $user->status = $request->input('status');

        $user->profile_picture = $profile_picture;
        $user->email_verified_at = now();
        $user->password = Hash::make($request->password);
        $user->percent = $request->input('percent');
        $user->addhaar_front = $aadhaar_front;
        $user->addhaar_back = $aadhaar_back;
        $user->designation = $request->input('designation');
        $user->salary = $request->input('salary');

        if ($request->input('role_id') && $request->input('assigned_manager_id')) {
            $user->assigned_manager_id = $request->input('assigned_manager_id');
        }

        // Handle KYC and other documents (multiple uploads)
        if ($request->hasFile('documents')) {
            $uploadedDocs = [];
            foreach ($request->file('documents') as $doc) {
                $filename = time() . '_' . $doc->getClientOriginalName();
                $doc->move(public_path('/uploads/documents/'), $filename);
                $uploadedDocs[] = $filename;
            }
            $user->documents = json_encode($uploadedDocs);
        }

        if ($request->input('user_type') == 'admin') {
            $user->branch_id = 50;  // Dumraon Main Branch
        } else {
            $user->branch_id = $request->input('branch_id');
        }

        // dd($user);
        DB::transaction(function () use ($user) {
            $user->save();
        });

        // Format response for table or return
        $user->status = user_status($user->status);
        $user->user_type = strtoupper($user->user_type);
        $user->role_id = strtolower((string) $user->user_type) === 'admin'
            ? _lang('Admin')
            : ($user->role->name ?? '');
        $user->profile_picture = '<img src="' . profile_picture($user->profile_picture) . '" class="thumb-sm mr-2">';

        if (!$request->ajax()) {
            return redirect()->route('users.index')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json([
                'result' => 'success',
                'action' => 'store',
                'message' => _lang('Saved Successfully'),
                'data' => $user,
                'table' => '#users_table'
            ]);
        }
    }


























    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $user = User::find($id);
        if (!$request->ajax()) {
            return view('backend.user.view', compact('user', 'id'));
        } else {
            return view('backend.user.modal.view', compact('user', 'id'));
        }

    }



// user profile show
public function userProfile(Request $request) {
    $user = Auth::user();

    $agentCommissions = \App\Models\CommissionRecord::with(['investment', 'sourceMember'])
        ->where('recipient_type', 'user')
        ->where('recipient_id', $user->id)
        ->where('level', 0)
        ->latest()
        ->take(10)
        ->get();
    $agentCommissionTotal = (float) \App\Models\CommissionRecord::where('recipient_type', 'user')
        ->where('recipient_id', $user->id)
        ->where('level', 0)
        ->sum('amount');
    $profileMember = $user->user_type === 'customer'
        ? \App\Models\Member::withoutGlobalScopes()->where('user_id', $user->id)->first()
        : null;
    $referralCommissions = $profileMember
        ? \App\Models\CommissionRecord::with(['investment', 'sourceMember'])
            ->where('recipient_type', 'member')
            ->where('recipient_id', $profileMember->id)
            ->latest()
            ->take(10)
            ->get()
        : collect();
    $referralCommissionTotal = $profileMember
        ? (float) \App\Models\CommissionRecord::where('recipient_type', 'member')
            ->where('recipient_id', $profileMember->id)
            ->sum('amount')
        : 0;

    $loans = Loan::select('loans.*')
    ->with('borrower')        // Loan ke borrower relation
    ->with('currency')        // Loan ke currency relation
    ->with('loan_product')    // Loan ke loan product relation
    ->orderBy('loans.id', 'desc')
    ->get();


   // dd($user);
    return view('layouts.menus.user_profile', compact(
        'user', 'loans', 'agentCommissions', 'agentCommissionTotal',
        'profileMember', 'referralCommissions', 'referralCommissionTotal'
    ));
}



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {

        $user = User::find($id);
        if (!$request->ajax()) {
            // $managers = User::whereHas('role', function($query) {
            //     $query->where('name', 'manager');
            // })->get();
            $managers = User::where('role_id', 31)->get();

            //dd($user);
            return view('backend.user.edit', compact('user', 'id', 'managers'));
        } else {
            return back();
        }
    }

    private function profileIdPrefix(User $user): ?string
    {
        if ($user->user_type !== 'user') {
            return null;
        }

        $roleName = strtolower(trim((string) DB::table('roles')->where('id', $user->role_id)->value('name')));

        if (str_contains($roleName, 'dsa')) {
            return 'DSA';
        }

        if (str_contains($roleName, 'employee') || str_contains($roleName, 'manager')) {
            return 'EMP';
        }

        return 'BUS';
    }

    private function nextProfileId(User $user, ?int $ignoreUserId = null): ?string
    {
        $prefix = $this->profileIdPrefix($user);

        return $prefix ? \App\Support\SerialNumberGenerator::next($prefix) : null;
    }

    /**
     * Update the specified resource in storage.

     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function update(Request $request, $id)
// {
//     //dd($request->all());
//     $validator = Validator::make($request->all(), [
//         'name'            => 'required|max:255',
//         'email'           => [
//             'required',
//             'email',
//             Rule::unique('users')->ignore($id),
//         ],
//         'user_type'       => 'required',
//         'status'          => 'required',
//         'mobile_number'   => [
//             'required',
//             Rule::unique('users')->ignore($id),
//         ],
//         'profile_picture' => 'nullable|image',
//         'password'        => 'nullable|min:6',
//         'assigned_manager_id' => 'required_if:role_id,agent',
//         'documents.*'     => 'nullable|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',

//         // 👇 New Fields Validation
//         'middle_name'        => 'nullable|string|max:255',

//         'last_name'          => 'nullable|string|max:255',
//         'whatsapp_number'    => 'nullable|string|max:20',
//         'aadhar_photo.*'     => 'nullable|mimes:jpg,jpeg,png,avif,pdf|max:2048',
//         'aadhar_number'      => ['nullable', 'regex:/^\d{4}\s?\d{4}\s?\d{4}$/'],
//         'pan_photo'          => 'nullable|mimes:jpg,jpeg,png,avif|max:2048',
//        'pan_number'         => ['nullable', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'],
//         'gst_type'           => 'nullable|in:Regular,Composition',
//         'gst_number'         => 'nullable|string|max:20',
//         'residence_address'  => 'nullable|string|max:1000',
//         'office_address'     => 'nullable|string|max:1000',
//         'account_type'       => 'nullable|in:Savings,Current',
//         'account_name'       => 'nullable|string|max:255',
//         'bank_name'          => 'nullable|string|max:255',
//         'ifsc_code'          => 'nullable|string|max:20',
//         'account_number'     => 'nullable|string|max:50',
//         'percent'              => 'nullable|numeric|min:0|max:100',
//     ]);

//        $messages = [
//         'aadhaar_number.regex' => 'Enter a valid 12-digit Aadhaar number (e.g. 1234 5678 9123)',
//         'pan_number.regex' => 'Enter a valid PAN number in format ABCDE1234F',
//     ];




//     if ($validator->fails()) {
//         if ($request->ajax()) {
//             return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
//         } else {
//             return redirect()->route('users.edit', $id)
//                 ->withErrors($validator)
//                 ->withInput();
//         }
//     }

//     if ($request->hasfile('profile_picture')) {
//         $file = $request->file('profile_picture');
//         $profile_picture = time() . $file->getClientOriginalName();
//         $file->move(public_path() . "/uploads/profile/", $profile_picture);
//     }

//     $user = User::find($id);
//     $user->name = $request->input('name');
//     $user->email = $request->input('email');
//     $user->mobile_number = $request->input('mobile_number');
//     $user->user_type = $request->input('user_type');
//     $user->role_id = $request->input('role_id');
//     $user->branch_id = $request->input('branch_id');
//     $user->status = $request->input('status');

//     if ($request->input('role_id') && $request->input('assigned_manager_id')) {
//         $user->assigned_manager_id = $request->input('assigned_manager_id');
//     } else {
//         $user->assigned_manager_id = null;
//     }

//     if ($request->hasfile('profile_picture')) {
//         $user->profile_picture = $profile_picture;
//     }

//     if ($request->password) {
//         $user->password = Hash::make($request->password);
//     }


//     // if ($request->hasfile('documents')) {
//     //     $documents = [];
//     //     foreach ($request->file('documents') as $file) {
//     //         $filename = time() . '_' . $file->getClientOriginalName();
//     //         $file->move(public_path('uploads/documents'), $filename);
//     //         $documents[] = $filename;
//     //     }
//     //     $user->documents = json_encode($documents);
//     // }
//     // Load existing documents (ensure array)
// $existingDocs = is_array($user->documents)
// ? $user->documents
// : (json_decode($user->documents, true) ?? []);

// // 1. Remove deleted files
// if ($request->has('remove_old_files')) {
// foreach ($request->remove_old_files as $removeFile) {
//     if (($key = array_search($removeFile, $existingDocs)) !== false) {
//         unset($existingDocs[$key]);

//         // ALSO delete file from server
//         $file_path = public_path('uploads/documents/' . $removeFile);
//         if (file_exists($file_path)) {
//             unlink($file_path);
//         }
//     }
// }
// }

// // 2. Add new uploaded files
// if ($request->hasfile('documents')) {
// foreach ($request->file('documents') as $file) {
//     $filename = time() . '_' . $file->getClientOriginalName();
//     $file->move(public_path('uploads/documents'), $filename);
//     $existingDocs[] = $filename;  // <-- MERGE HERE
// }
// }

// // Final JSON encode
// $user->documents = json_encode(array_values($existingDocs));



//     $user->middle_name        = $request->middle_name;
//     $user->last_name          = $request->last_name;
//     $user->whatsapp_number    = $request->whatsapp_number;
//     $user->aadhar_number     = $request->aadhar_number;
//     $user->pan_number         = $request->pan_number;
//     $user->gst_type           = $request->gst_type;
//     $user->gst_number         = $request->gst_number;
//     $user->residence_address  = $request->residence_address;
//     $user->office_address     = $request->office_address;
//     $user->account_type       = $request->account_type;
//     $user->account_name       = $request->account_name;
//     $user->bank_name          = $request->bank_name;
//     $user->ifsc_code          = $request->ifsc_code;
//     $user->account_number     = $request->account_number;
//     $user->percent            = $request->percent;

//     // if ($request->hasfile('aadhar_photo')) {
//     //     $aadhar_photos = [];
//     //     foreach ($request->file('aadhar_photo') as $file) {
//     //         $filename = time() . '_' . $file->getClientOriginalName();
//     //         $file->move(public_path('uploads/documents'), $filename);
//     //         $aadhar_photos[] = $filename;
//     //     }
//     //     $user->aadhar_photo = json_encode($aadhar_photos);
//     // }

//     //   if ($request->hasFile('aadhaar_front')) {
//     //     $file = $request->file('addhaar_front');
//     //     $addhaarFront = time() . '_front.' . $file->getClientOriginalExtension();
//     //     $file->move(public_path('uploads/aadhaar'), $addhaarFront);
//     //     $user->aadhaar_front = $addhaarFront;
//     // }

//     // if ($request->hasFile('aadhaar_back')) {
//     //     $file = $request->file('aadhaar_back');
//     //     $aadhaarBack = time() . '_back.' . $file->getClientOriginalExtension();
//     //     $file->move(public_path('uploads/aadhaar'), $aadhaarBack);
//     //     $user->aadhaar_back = $aadhaarBack;
//     // }


// // Aadhaar Front Upload
// if ($request->hasFile('addhaar_front')) {
//     $file = $request->file('addhaar_front');
//     $filename = time() . '_addhaar_front_' . $file->getClientOriginalName();
//     $file->move(public_path('uploads/documents'), $filename);
//     $user->addhaar_front = $filename;
// }

// // Aadhaar Back Upload
// if ($request->hasFile('addhaar_back')) {
//     $file = $request->file('addhaar_back');
//     $filename = time() . '_addhaar_back_' . $file->getClientOriginalName();
//     $file->move(public_path('uploads/documents'), $filename);
//     $user->addhaar_back = $filename;
// }



//     if ($request->hasfile('pan_photo')) {
//         $file = $request->file('pan_photo');
//         $filename = time() . '_' . $file->getClientOriginalName();
//         $file->move(public_path('uploads/documents'), $filename);
//         $user->pan_photo = $filename;
//     }

//     if ($request->input('user_type')=='admin'){
//         $user->branch_id = 50;
//     }
//     else{
//         $user->branch_id = $request->input('branch_id');
//     }
//     // dd($user);
//     $user->save();

//     // Prefix Output
//     $user->status = user_status($user->status);
//     $user->user_type = strtoupper($user->user_type);
//     $user->role_id = $user->role->name;
//     $user->profile_picture = '<img src="' . profile_picture($user->profile_picture) . '" class="thumb-sm mr-2">';

//     if (!$request->ajax()) {
//         return redirect()->route('users.index')->with('success', _lang('Updated Sucessfully'));
//     } else {
//         return response()->json([
//             'result' => 'success',
//             'action' => 'update',
//             'message' => _lang('Updated Sucessfully'),
//             'data' => $user,
//             'table' => '#users_table'
//         ]);
//     }
// }







//  new update
// public function update(Request $request, $id)
// {
//     // ---------------- VALIDATION ----------------

//     $validator = Validator::make($request->all(), [
//         'name'            => 'required|max:255',
//         'email'           => ['required','email', Rule::unique('users')->ignore($id)],
//         'user_type'       => 'required',
//         'status'          => 'required',
//         'mobile_number'   => ['required', Rule::unique('users')->ignore($id)],
//         'profile_picture' => 'nullable|image',
//         'password'        => 'nullable|min:6',
//         'assigned_manager_id' => 'required_if:role_id,agent',

//         'documents.*'     => 'nullable|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',

//         'middle_name'        => 'nullable|string|max:255',
//         'last_name'          => 'nullable|string|max:255',
//         'whatsapp_number'    => 'nullable|string|max:20',
//         'aadhar_photo.*'     => 'nullable|mimes:jpg,jpeg,png,avif,pdf|max:2048',
//         'aadhar_number'      => ['nullable','regex:/^\d{4}\s?\d{4}\s?\d{4}$/'],
//         'pan_photo'          => 'nullable|mimes:jpg,jpeg,png,avif|max:2048',
//         'pan_number'         => ['nullable','regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'],
//         'gst_type'           => 'nullable|in:Regular,Composition',
//         'gst_number'         => 'nullable|string|max:20',
//         'residence_address'  => 'nullable|string|max:1000',
//         'office_address'     => 'nullable|string|max:1000',
//         'account_type'       => 'nullable|in:Savings,Current',
//         'account_name'       => 'nullable|string|max:255',
//         'bank_name'          => 'nullable|string|max:255',
//         'ifsc_code'          => 'nullable|string|max:20',
//         'account_number'     => 'nullable|string|max:50',
//         'percent'            => 'nullable|numeric|min:0|max:100',
//     ]);

//     if ($validator->fails()) {
//         return $request->ajax()
//             ? response()->json(['result'=>'error','message'=>$validator->errors()->all()])
//             : redirect()->route('users.edit',$id)->withErrors($validator)->withInput();
//     }

//     // ---------------- FETCH USER ----------------

//     $user = User::findOrFail($id);

//     // ---------------- PROFILE PICTURE ----------------
//     if ($request->hasFile('profile_picture')) {
//         $file = $request->file('profile_picture');
//         $profile_picture = time() . '_' . $file->getClientOriginalName();
//         $file->move(public_path('uploads/profile'), $profile_picture);
//         $user->profile_picture = $profile_picture;
//     }

//     // ---------------- BASIC FIELDS ----------------

//     $user->name = $request->name;
//     $user->email = $request->email;
//     $user->mobile_number = $request->mobile_number;
//     $user->user_type = $request->user_type;
//     $user->role_id = $request->role_id;
//     $user->branch_id = $request->user_type == 'admin' ? 50 : $request->branch_id;
//     $user->status = $request->status;

//     $user->assigned_manager_id =
//         ($request->role_id && $request->assigned_manager_id)
//         ? $request->assigned_manager_id
//         : null;

//     if ($request->filled('password')) {
//         $user->password = Hash::make($request->password);
//     }

//     // ---------------- DOCUMENTS: OLD + NEW MERGE ----------------

//     // Convert to array
//     $existingDocs = is_array($user->documents)
//         ? $user->documents
//         : (json_decode($user->documents, true) ?? []);

//     // 1. REMOVE FILES user deleted from frontend
//     if ($request->has('remove_old_files')) {
//         foreach ($request->remove_old_files as $removeFile) {

//             if (($key = array_search($removeFile, $existingDocs)) !== false) {
//                 unset($existingDocs[$key]);
//             }

//             $filePath = public_path('uploads/documents/' . $removeFile);
//             if (file_exists($filePath)) {
//                 unlink($filePath);
//             }
//         }
//     }

//     // 2. ADD NEW FILES
//     if ($request->hasFile('documents')) {
//         foreach ($request->file('documents') as $file) {
//             $filename = time() . '_' . $file->getClientOriginalName();
//             $file->move(public_path('uploads/documents'), $filename);
//             $existingDocs[] = $filename;
//         }
//     }

//     // Save final JSON
//     $user->documents = json_encode(array_values($existingDocs));

//     // ---------------- OTHER SINGLE FILE UPLOADS ----------------

//     if ($request->hasFile('addhaar_front')) {
//         $file = $request->file('addhaar_front');
//         $filename = time().'_front_'.$file->getClientOriginalName();
//         $file->move(public_path('uploads/documents'), $filename);
//         $user->addhaar_front = $filename;
//     }

//     if ($request->hasFile('addhaar_back')) {
//         $file = $request->file('addhaar_back');
//         $filename = time().'_back_'.$file->getClientOriginalName();
//         $file->move(public_path('uploads/documents'), $filename);
//         $user->addhaar_back = $filename;
//     }

//     if ($request->hasFile('pan_photo')) {
//         $file = $request->file('pan_photo');
//         $filename = time().'_'.$file->getClientOriginalName();
//         $file->move(public_path('uploads/documents'), $filename);
//         $user->pan_photo = $filename;
//     }

//     // ---------------- OTHER FIELDS ----------------

//     $user->middle_name = $request->middle_name;
//     $user->last_name = $request->last_name;
//     $user->whatsapp_number = $request->whatsapp_number;
//     $user->aadhar_number = $request->aadhar_number;
//     $user->pan_number = $request->pan_number;
//     $user->gst_type = $request->gst_type;
//     $user->gst_number = $request->gst_number;
//     $user->residence_address = $request->residence_address;
//     $user->office_address = $request->office_address;
//     $user->account_type = $request->account_type;
//     $user->account_name = $request->account_name;
//     $user->bank_name = $request->bank_name;
//     $user->ifsc_code = $request->ifsc_code;
//     $user->account_number = $request->account_number;
//     $user->percent = $request->percent;

//     // ---------------- SAVE ----------------

//     $user->save();

//     if ($request->ajax()) {
//         return response()->json([
//             'result' => 'success',
//             'action' => 'update',
//             'message' => 'Updated Successfully',
//             'data' => $user,
//             'table' => '#users_table'
//         ]);
//     }

//     return redirect()->route('users.index')->with('success','Updated Successfully');
// }
 public function update(Request $request, $id)
    {
        // ---------------- VALIDATION ----------------

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            'user_type' => 'required',
            'status' => 'required',
            'mobile_number' => ['required', Rule::unique('users')->ignore($id)],
            'profile_picture' => 'nullable|image',
            'password' => 'nullable|min:6',
            'assigned_manager_id' => 'required_if:role_id,agent',
            'documents.*' => 'nullable|mimes:pdf,jpg,jpeg,png,doc,docx',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:20',
            'aadhar_photo.*' => 'nullable|mimes:jpg,jpeg,png,avif,pdf|max:2048',
            'aadhar_number' => ['nullable', 'regex:/^\d{4}\s?\d{4}\s?\d{4}$/'],
            'pan_photo' => 'nullable|mimes:jpg,jpeg,png,avif|max:2048',
            'pan_number' => ['nullable', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'],
            'gst_type' => 'nullable|in:Regular,Composition',
            'gst_number' => 'nullable|string|max:20',
            'residence_address' => 'nullable|string|max:1000',
            'office_address' => 'nullable|string|max:1000',
            'account_type' => 'nullable|in:Savings,Current',
            'account_name' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:20',
            'account_number' => 'nullable|string|max:50',
            'percent' => 'nullable|numeric|min:0|max:100',
            'designation' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return $request->ajax()
                ? response()->json(['result' => 'error', 'message' => $validator->errors()->all()])
                : redirect()->route('users.edit', $id)->withErrors($validator)->withInput();
        }

        // ---------------- FETCH USER ----------------

        $user = User::findOrFail($id);

        // ---------------- PROFILE PICTURE ----------------
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $profile_picture = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/profile'), $profile_picture);
            $user->profile_picture = $profile_picture;
        }

        // ---------------- BASIC FIELDS ----------------

        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile_number = $request->mobile_number;
        $user->user_type = $request->user_type;
        $user->role_id = $request->role_id;
        $user->branch_id = $request->user_type == 'admin' ? 50 : $request->branch_id;
        $user->status = $request->status;

        $user->assigned_manager_id =
            ($request->role_id && $request->assigned_manager_id)
                ? $request->assigned_manager_id
                : null;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // ---------------- DOCUMENTS: OLD + NEW MERGE ----------------

        // 1. Get old files user wants to keep
        $existingDocs = $request->input('old_files', []);  // old files that were not deleted

        // 2. Remove old files user marked for deletion
        if ($request->has('remove_old_files')) {
            foreach ($request->remove_old_files as $removeFile) {
                $filePath = public_path('uploads/documents/' . $removeFile);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        // 3. Add new uploaded files
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/documents'), $filename);
                $existingDocs[] = $filename;
            }
        }

        // Save final JSON
        $user->documents = json_encode(array_values($existingDocs));

        // ---------------- OTHER SINGLE FILE UPLOADS ----------------

        if ($request->hasFile('addhaar_front')) {
            $file = $request->file('addhaar_front');
            $filename = time() . '_front_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/documents'), $filename);
            $user->addhaar_front = $filename;
        }

        if ($request->hasFile('addhaar_back')) {
            $file = $request->file('addhaar_back');
            $filename = time() . '_back_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/documents'), $filename);
            $user->addhaar_back = $filename;
        }

        if ($request->hasFile('pan_photo')) {
            $file = $request->file('pan_photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/documents'), $filename);
            $user->pan_photo = $filename;
        }

        // ---------------- OTHER FIELDS ----------------

        $user->middle_name = $request->middle_name;
        $user->last_name = $request->last_name;
        $user->whatsapp_number = $request->whatsapp_number;
        $user->aadhar_number = $request->aadhar_number;
        $user->pan_number = $request->pan_number;
        $user->gst_type = $request->gst_type;
        $user->gst_number = $request->gst_number;
        $user->residence_address = $request->residence_address;
        $user->office_address = $request->office_address;
        $user->account_type = $request->account_type;
        $user->account_name = $request->account_name;
        $user->bank_name = $request->bank_name;
        $user->ifsc_code = $request->ifsc_code;
        $user->account_number = $request->account_number;
        $user->percent = $request->percent;
        $user->designation = $request->designation;
        $user->salary = $request->salary;

        // ---------------- SAVE ----------------

        DB::transaction(function () use ($user) {
            $expectedPrefix = $this->profileIdPrefix($user);
            if ($expectedPrefix === null) {
                $user->user_id = null;
            } elseif (!preg_match('/^' . $expectedPrefix . '-\d+$/', (string) $user->user_id)) {
                $user->user_id = $this->nextProfileId($user, $user->id);
            }

            $user->save();
        });

        if ($request->ajax()) {
            return response()->json([
                'result' => 'success',
                'action' => 'update',
                'message' => 'Updated Successfully',
                'data' => $user,
                'table' => '#users_table'
            ]);
        }

        return redirect()->route('users.index')->with('success', 'Updated Successfully');
    }






    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $user = User::find($id);
        $user->delete();
        return redirect()->route('users.index')->with('success', _lang('Deleted Sucessfully'));
    }

    /**
     * Show agents assigned to a manager
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function myAgents() {
        $user = auth()->user();

        // Check if user is a manager or admin
        if (!$user->role || (strtolower($user->role->name) !== 'manager' && $user->user_type !== 'admin')) {
            return redirect()->route('dashboard')->with('error', _lang('Access Denied'));
        }

        $agents = User::where('assigned_manager_id', $user->id)
            ->whereHas('role', function($q) { $q->where('name', 'agent'); })
            ->with(['role', 'branch'])
            ->orderBy('name', 'asc')
            ->get();

        return view('backend.user.my_agents', compact('agents'));
    }
}
