<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $employees = User::whereHas('role', function ($q) {
            $q->where('name', 'Employee');
        })->get();

        foreach ($employees as $employee) {
            $employee->current_month_count = Attendance::where('user_id', $employee->id)
                ->whereMonth('date', date('m'))
                ->whereYear('date', date('Y'))
                ->whereIn('status', ['Present', 'Late'])
                ->count();
        }

        return view('backend.attendance.list', compact('employees'));
    }

    public function user_attendance(Request $request, $user_id)
    {
        $employee = User::findOrFail($user_id);
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $attendances = Attendance::where('user_id', $user_id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'asc')
            ->get();

        return view('backend.attendance.user_attendance', compact('employee', 'attendances', 'month', 'year'));
    }

    public function create(Request $request)
    {
        $users = User::whereHas('role', function ($q) {
            $q->where('name', 'Employee');
        })->get();

        if (!$request->ajax()) {
            return view('backend.attendance.create', compact('users'));
        } else {
            return view('backend.attendance.modal.create', compact('users'));
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'date' => 'required|date',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        $attendance = new Attendance();
        $attendance->user_id = $request->input('user_id');
        $attendance->date = $request->input('date');
        $attendance->login_time = $request->input('login_time');
        $attendance->logout_time = $request->input('logout_time');
        $attendance->status = $request->input('status');
        $attendance->note = $request->input('note');
        $attendance->save();

        if (!$request->ajax()) {
            return redirect()->route('attendances.index')->with('success', _lang('Attendance Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Attendance Saved Successfully'), 'data' => $attendance]);
        }
    }

    public function edit(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        $users = User::whereHas('role', function ($q) {
            $q->where('name', 'Employee');
        })->get();

        if (!$request->ajax()) {
            return view('backend.attendance.edit', compact('attendance', 'users'));
        } else {
            return view('backend.attendance.modal.edit', compact('attendance', 'users'));
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'date' => 'required|date',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        $attendance = Attendance::findOrFail($id);
        $attendance->user_id = $request->input('user_id');
        $attendance->date = $request->input('date');
        $attendance->login_time = $request->input('login_time');
        $attendance->logout_time = $request->input('logout_time');
        $attendance->status = $request->input('status');
        $attendance->note = $request->input('note');
        $attendance->save();

        if (!$request->ajax()) {
            return redirect()->route('attendances.index')->with('success', _lang('Attendance Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Attendance Updated Successfully'), 'data' => $attendance]);
        }
    }

    public function show(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        if (!$request->ajax()) {
            return view('backend.attendance.view', compact('attendance'));
        } else {
            return view('backend.attendance.modal.view', compact('attendance'));
        }
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();
        return redirect()->route('attendances.index')->with('success', _lang('Deleted Successfully'));
    }
}
