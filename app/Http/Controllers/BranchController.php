<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        date_default_timezone_set(get_option('timezone', 'Asia/Dhaka'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index() {
    //     $branchs = Branch::all()->sortByDesc("id");
    //     return view('backend.branch.list', compact('branchs'));
    // }
    
public function index(Request $request) {
    $sort = $request->get('sort', 'default'); // default = newest first

    if ($sort === 'az') {
        $branchs = Branch::orderBy('name', 'ASC')->get();
    } elseif ($sort === 'za') {
        $branchs = Branch::orderBy('name', 'DESC')->get();
    } else {
        $branchs = Branch::orderBy('id', 'DESC')->get(); // default newest first
    }

    return view('backend.branch.list', compact('branchs', 'sort'));
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        if (!$request->ajax()) {
            return view('backend.branch.create');
        } else {
            return view('backend.branch.modal.create');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'contact_email' => 'nullable|email',
            'state'         => 'nullable|string|max:255',
            'district'      => 'nullable|string|max:255',
            'pincode'       => 'nullable|string|max:10',
            'branch_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'agreements.*'  => 'nullable|file|mimes:pdf,jpeg,png,jpg,gif,webp|max:10240',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('branches.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $branch                = new Branch();
        $branch->name          = $request->input('name');
        $branch->contact_email = $request->input('contact_email');
        $branch->contact_phone = $request->input('contact_phone');
        $branch->address       = $request->input('address');
        $branch->descriptions  = $request->input('descriptions');
        $branch->state         = $request->input('state');
        $branch->district      = $request->input('district');
        $branch->pincode       = $request->input('pincode');

        if ($request->hasFile('branch_image')) {
            $file = $request->file('branch_image');
            $imageName = 'branch_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/media/'), $imageName);
            $branch->branch_image = $imageName;
        }

        if ($request->hasFile('agreements')) {
            $agreementFiles = [];
            foreach ($request->file('agreements') as $file) {
                $fileName = 'agreement_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('uploads/media/'), $fileName);
                $agreementFiles[] = $fileName;
            }
            $branch->agreements = json_encode($agreementFiles);
        }

        $branch->save();

        if (!$request->ajax()) {
            return redirect()->route('branches.create')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $branch, 'table' => '#branches_table']);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $branch = Branch::find($id);
        if (!$request->ajax()) {
            return view('backend.branch.view', compact('branch', 'id'));
        } else {
            return view('backend.branch.modal.view', compact('branch', 'id'));
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $branch = Branch::find($id);
        if (!$request->ajax()) {
            return view('backend.branch.edit', compact('branch', 'id'));
        } else {
            return view('backend.branch.modal.edit', compact('branch', 'id'));
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'contact_email' => 'nullable|email',
            'state'         => 'nullable|string|max:255',
            'district'      => 'nullable|string|max:255',
            'pincode'       => 'nullable|string|max:10',
            'branch_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'agreements.*'  => 'nullable|file|mimes:pdf,jpeg,png,jpg,gif,webp|max:10240',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('branches.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $branch                = Branch::find($id);
        $branch->name          = $request->input('name');
        $branch->contact_email = $request->input('contact_email');
        $branch->contact_phone = $request->input('contact_phone');
        $branch->address       = $request->input('address');
        $branch->descriptions  = $request->input('descriptions');
        $branch->state         = $request->input('state');
        $branch->district      = $request->input('district');
        $branch->pincode       = $request->input('pincode');

        if ($request->hasFile('branch_image')) {
            $file = $request->file('branch_image');
            $imageName = 'branch_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/media/'), $imageName);
            $branch->branch_image = $imageName;
        }

        if ($request->hasFile('agreements')) {
            $existing = json_decode($branch->agreements, true);
            $agreementFiles = is_array($existing) ? $existing : [];
            foreach ($request->file('agreements') as $file) {
                $fileName = 'agreement_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('uploads/media/'), $fileName);
                $agreementFiles[] = $fileName;
            }
            $branch->agreements = json_encode($agreementFiles);
        }

        $branch->save();

        if (!$request->ajax()) {
            return redirect()->route('branches.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $branch, 'table' => '#branches_table']);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $branch = Branch::find($id);
        $branch->delete();
        return redirect()->route('branches.index')->with('success', _lang('Deleted Successfully'));
    }
}