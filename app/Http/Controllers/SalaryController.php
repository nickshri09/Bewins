<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalaryController extends Controller
{
    public function get_attendance(Request $request)
    {
        $user_id = $request->user_id;
        $month_name = $request->month;
        $year = $request->year;

        $month = date('m', strtotime($month_name));

        // Get total days in month
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // Count Sundays
        $sundays = 0;
        for ($day = 1; $day <= $days_in_month; $day++) {
            if (date('N', strtotime("$year-$month-$day")) == 7) {
                $sundays++;
            }
        }

        $total_working_days = $days_in_month - $sundays;

        $presents = Attendance::where('user_id', $user_id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereIn('status', ['Present', 'Late'])
            ->count();

        $absents = $total_working_days - $presents;
        if ($absents < 0)
            $absents = 0;

        return response()->json([
            'total_days' => $days_in_month,
            'sundays' => $sundays,
            'total_working_days' => $total_working_days,
            'presents' => $presents,
            'absents' => $absents,
        ]);
    }

    public function index()
    {
        $salaries = Salary::with('user')->orderBy('id', 'desc')->get();
        return view('backend.salary.list', compact('salaries'));
    }

    public function create(Request $request)
    {
        $users = User::whereHas('role', function ($q) {
            $q->where('name', 'Employee');
        })->get();

        if (!$request->ajax()) {
            return view('backend.salary.modal.create', compact('users'));
        } else {
            return view('backend.salary.modal.create', compact('users'));
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'month' => 'required',
            'year' => 'required|integer',
            'amount' => 'required|numeric',
            'status' => 'required',
            'payment_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()
                    ->route('salaries.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        // Ensure user is an employee
        $user = User::findOrFail($request->input('user_id'));
        if (!$user->role || strtolower($user->role->name) !== 'employee') {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => [_lang('Salary management is only allowed for employees.')]]);
            } else {
                return redirect()->back()->with('error', _lang('Salary management is only allowed for employees.'))->withInput();
            }
        }

        $salary = new Salary();
        $salary->user_id = $request->input('user_id');
        $salary->month = $request->input('month');
        $salary->year = $request->input('year');
        $salary->amount = $request->input('amount');
        $salary->status = $request->input('status');
        $salary->payment_date = $request->input('payment_date');
        $salary->note = $request->input('note');
        $salary->save();

        if (!$request->ajax()) {
            return redirect()->route('salaries.index')->with('success', _lang('Salary Paid Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Salary Paid Successfully'), 'data' => $salary, 'table' => '#salaries_table']);
        }
    }

    public function show(Request $request, $id)
    {
        $salary = Salary::findOrFail($id);
        if (!$request->ajax()) {
            return view('backend.salary.view', compact('salary'));
        } else {
            return view('backend.salary.modal.view', compact('salary'));
        }
    }

    public function edit(Request $request, $id)
    {
        $salary = Salary::findOrFail($id);
        $users = User::whereHas('role', function ($q) {
            $q->where('name', 'Employee');
        })->get();

        if (!$request->ajax()) {
            return view('backend.salary.edit', compact('salary', 'users'));
        } else {
            return view('backend.salary.modal.edit', compact('salary', 'users'));
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'month' => 'required',
            'year' => 'required|integer',
            'amount' => 'required|numeric',
            'status' => 'required',
            'payment_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()
                    ->route('salaries.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        // Ensure user is an employee
        $user = User::findOrFail($request->input('user_id'));
        if (!$user->role || strtolower($user->role->name) !== 'employee') {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => [_lang('Salary management is only allowed for employees.')]]);
            } else {
                return redirect()->back()->with('error', _lang('Salary management is only allowed for employees.'))->withInput();
            }
        }

        $salary = Salary::findOrFail($id);
        $salary->user_id = $request->input('user_id');
        $salary->month = $request->input('month');
        $salary->year = $request->input('year');
        $salary->amount = $request->input('amount');
        $salary->status = $request->input('status');
        $salary->payment_date = $request->input('payment_date');
        $salary->note = $request->input('note');
        $salary->save();

        if (!$request->ajax()) {
            return redirect()->route('salaries.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $salary, 'table' => '#salaries_table']);
        }
    }

    public function destroy($id)
    {
        $salary = Salary::findOrFail($id);
        $salary->delete();
        return redirect()->route('salaries.index')->with('success', _lang('Deleted Successfully'));
    }
}
