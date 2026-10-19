<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomService; // ✅ Make sure this line exists

class CustomServiceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'custom_service_name' => 'nullable|string|max:255',
            'service_id' => 'required|exists:services,id',
        ]);

        CustomService::create([
            'custom_service_name' => $request->custom_service_name,
            'service_id' => $request->service_id,
        ]);

        return redirect()->back()->with('success', 'Service saved successfully!');
    }
    
public function index()
{  
    $customServices = CustomService::with('service')->get(); // Assuming there is a relation
    return view('layouts.menus.custom-services', compact('customServices'));
}

}
