<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewWithdrowRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawRequestController extends Controller
{
    public function send(Request $request){
        //dd($request->all());
        $user=Auth::user();
       $validator = $request->validate([
            'coin_transfer'=>'required|numeric',
            'holder_name' =>'required|string',
            'bank_name'   =>'required|string',
            'account_number'=>'required|string',
            'ifsc_code' => 'required|string',
            'account_type'=>'required|string',
            'status'    => 'requrire',
        ]);
         //dd($validator);
        // dd($request->all(),$user);
        NewWithdrowRequest::create([
            'user_id'=> $user->id,
            'coin_transfer'=>$request->coin_transfer,
            'holder_name'=> $request->holder_name,
            'bank_name'  => $request->bank_name,
            'account_number'=>$request->account_number,
            'ifsc_code' => $request->ifsc_code,
            'account_type'=>$request->account_type,
            'status'    => '1',
        ]);

        return redirect()->route('getrequest.index')->with('success', 'FD Created Successfully!');

      }


    //   public function index(){

    //     $list=NewWithdrowRequest::all();
    //     //dd($list);
    //     return view('backend.withdrow.list',compact('list'));

    //   }

    public function index()
    {
        if (auth()->user()->user_type == 'admin') {

            // Admin → show all (latest first)
            $list = NewWithdrowRequest::orderBy('id', 'DESC')->get();

        } else {

            // User → only his own (latest first)
            $list = NewWithdrowRequest::where('user_id', auth()->id())
                    ->orderBy('id', 'DESC')
                    ->get();
        }

        return view('backend.withdrow.list', compact('list'));
    }






    public function changeStatus(Request $req)
    {
       //dd($req->all());
        $req->validate([
            'withdraw_id' => 'required',
            'status'      => 'required',
            'remark'      => 'required',
        ]);

        $withdraw = NewWithdrowRequest::find($req->withdraw_id);

        if (!$withdraw) {
            return back()->with('error', 'Withdraw request not found');
        }

        $withdraw->status = $req->status;
        $withdraw->remark = $req->remark;
        $withdraw->save();

        return back()->with('success', 'Withdraw request updated successfully!');
    }





    public function getWithdraw($id)
{
    $withdraw = NewWithdrowRequest::find($id);

    if (!$withdraw) {
        return response()->json(['status' => false, 'msg' => 'Not found'], 404);
    }

    return response()->json([
        'status' => true,
        'data' => $withdraw
    ]);
}


public function update(Request $request)
{
    $request->validate([
        'withdraw_id'   => 'required',
        'coin_transfer' => 'required|numeric',
        'holder_name'   => 'required|string',
        'bank_name'     => 'required|string',
        'account_number'=> 'required|string',
        'ifsc_code'     => 'required|string',
        'account_type'  => 'required|string',
    ]);

    $withdraw = NewWithdrowRequest::find($request->withdraw_id);

    if (!$withdraw) {
        return back()->with('error', 'Withdraw request not found');
    }

    $withdraw->update([
        'coin_transfer' => $request->coin_transfer,
        'holder_name'   => $request->holder_name,
        'bank_name'     => $request->bank_name,
        'account_number'=> $request->account_number,
        'ifsc_code'     => $request->ifsc_code,
        'account_type'  => $request->account_type,
    ]);

    return back()->with('success', 'Withdraw request updated successfully!');
}



public function approvedlist()
{

    if (auth()->user()->user_type != 'admin') {
        abort(403, 'Unauthorized');
    }

    // Status = 2 (Approved) वाले requests latest first
    $list = NewWithdrowRequest::where('status', 2)
                ->orderBy('id', 'DESC')
                ->get();

    return view('backend.withdrow.approved_list', compact('list'));
}



}
