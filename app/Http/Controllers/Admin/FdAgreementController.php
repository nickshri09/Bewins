<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FdAgreementDocument;
use Illuminate\Http\Request;

class FdAgreementController extends Controller
{

    // Upload Form Modal
    public function create($fdId)
    {
        //dd('fdhdf');
        return view('fd.agreement.create', compact('fdId'));
    }

    // Store FD Agreement Files
    public function store(Request $request, $fdId)
    {
        //dd('jksdbg');
        $request->validate([
            'name' => 'required|string|max:255',
            'documents.*' => 'required|file|mimes:pdf,jpg,png,docx'
        ]);

        if ($request->hasFile('documents')) {
            foreach ($request->documents as $file) {
                $filename = time() . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/fd'), $filename);

                FdAgreementDocument::create([
                    'fd_id' => $fdId,
                    'name' => $request->name,
                    'file' => $filename
                ]);
            }
        }
        return response()->json([
            'result' => 'success',
            'message' => 'Documents Uploaded Successfully'
        ]);
    }
}


