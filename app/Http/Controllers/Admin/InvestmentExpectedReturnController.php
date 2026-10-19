<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvestmentExpectedReturn;
use App\Models\InvestmentExpectedReturnHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvestmentExpectedReturnController extends Controller
{
    // public function index()
    // {
    //     $data = InvestmentExpectedReturn::latest()->get();
    //     return view('investments.investment_expected_return.index', compact('data'));
    // }
    public function index(Request $request)
    {
        $type = $request->get('type', 'investment');
        $data = InvestmentExpectedReturn::with('histories')
            ->where('type', $type)
            ->latest()
            ->get();

        return view('investments.investment_expected_return.index', compact('data', 'type'));
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'investment');
        return view('investments.investment_expected_return.create', compact('type'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expected_return' => 'required|numeric|min:0',
            'type' => 'required|in:investment,fd,loan',
        ]);

        $record = InvestmentExpectedReturn::create([
            'type' => $request->type,
            'expected_return' => $request->expected_return,
            'status' => $request->status ?? 1,
        ]);

        // History on create
        InvestmentExpectedReturnHistory::create([
            'expected_return_id' => $record->id,
            'old_value' => null,
            'new_value' => $record->expected_return,
            'changed_by' => Auth::id()
        ]);

        return redirect()
            ->route('investment.expected-return.index', ['type' => $record->type])
            ->with('success', ucfirst($record->type) . ' expected return created successfully.');
    }

    public function edit($id)
    {
        $record = InvestmentExpectedReturn::findOrFail($id);
        return view('investments.investment_expected_return.edit', compact('record'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'expected_return' => 'required|numeric|min:0',
        ]);

        $record = InvestmentExpectedReturn::findOrFail($id);
        $oldValue = $record->expected_return;

        $record->update([
            'expected_return' => $request->expected_return,
            'status' => $request->status ?? 0,
        ]);

        // History on update
        InvestmentExpectedReturnHistory::create([
            'expected_return_id' => $record->id,
            'old_value' => $oldValue,
            'new_value' => $request->expected_return,
            'changed_by' => Auth::id()
        ]);

        return redirect()
            ->route('investment.expected-return.index', ['type' => $record->type])
            ->with('success', ucfirst($record->type) . ' expected return updated successfully.');
    }

    // Status toggle
    public function toggleStatus($id)
    {
        $record = InvestmentExpectedReturn::findOrFail($id);
        $record->status = !$record->status;
        $record->save();

        return back()->with('success', 'Status updated.');
    }

    // Optional: delete
    public function destroy($id)
    {
        $record = InvestmentExpectedReturn::findOrFail($id);
        $record->delete();

        return back()->with('success', 'Deleted successfully.');
    }
}
