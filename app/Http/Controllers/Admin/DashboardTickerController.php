<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DashboardTicker;
use Illuminate\Http\Request;

class DashboardTickerController extends Controller
{
    public function index()
    {
        $tickers = DashboardTicker::orderBy('id')->paginate(20);

        return view('backend.dashboard_tickers.index', compact('tickers'));
    }

    public function create()
    {
        return view('backend.dashboard_tickers.form', ['ticker' => new DashboardTicker()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        DashboardTicker::create($validated);

        return redirect()->route('admin.tickers.index')->with('success', _lang('Scrolling text saved successfully'));
    }

    public function edit(DashboardTicker $ticker)
    {
        return view('backend.dashboard_tickers.form', compact('ticker'));
    }

    public function update(Request $request, DashboardTicker $ticker)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        $ticker->update($validated);

        return redirect()->route('admin.tickers.index')->with('success', _lang('Scrolling text updated successfully'));
    }

    public function destroy(DashboardTicker $ticker)
    {
        $ticker->delete();

        return redirect()->route('admin.tickers.index')->with('success', _lang('Scrolling text deleted successfully'));
    }
}
