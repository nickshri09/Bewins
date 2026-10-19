<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MainService;

class BewinsMainServiceController extends Controller
{
    // public function index()
    // {
    //     $items = MainService::orderBy('service_code')->paginate(20);
    //     return view('layouts.menus.main_services.index', compact('items'));
    // }

    public function index(Request $request)
   {
    $query = MainService::query();

    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('service_code', 'like', '%' . $search . '%')
              ->orWhere('name', 'like', '%' . $search . '%');
        });
    }

    $items = $query->orderBy('service_code')->paginate(20);

    return view('layouts.menus.main_services.index', compact('items'));
   }

    public function create()
    {
        return view('layouts.menus.main_services.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_code' => 'required|string|max:255',
            'name' => 'required|string|max:1000',
            'amount' => 'required|numeric',
        ]);

        MainService::create($data);
        return redirect()->route('main-services.index')->with('success', 'Main service created');
    }

    public function edit($id)
    {
        $item = MainService::findOrFail($id);
        return view('layouts.menus.main_services.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = MainService::findOrFail($id);
        $data = $request->validate([
            'service_code' => 'required|string|max:255',
            'name' => 'required|string|max:1000',
            'amount' => 'required|numeric',
        ]);

        $item->update($data);
        return redirect()->route('main-services.index')->with('success', 'Main service updated');
    }

    public function destroy($id)
    {
        $item = MainService::findOrFail($id);
        $item->delete();
        return redirect()->route('main-services.index')->with('success', 'Main service deleted');
    }
}
