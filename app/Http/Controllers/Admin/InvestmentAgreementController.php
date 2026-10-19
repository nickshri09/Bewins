<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\InvestmentAgreementDocument;
use App\Services\AgreementEmailService;
use Illuminate\Http\Request;

class InvestmentAgreementController extends Controller
{
    public function create($investmentId)
    {

        // dd('fdhgfjdhfkdf');
        return view('investments.agreement.create', compact('investmentId'));

    }

    public function store(Request $request, $investmentId)
    {
        //dd($request->all(), $investmentId );
        $request->validate([
            'name' => 'required|string|max:255',
            'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx',
        ]);

        foreach ($request->file('documents') as $file) {
            $filename = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('uploads/investments'), $filename);

            InvestmentAgreementDocument::create([
                'investment_id' => $investmentId,
                'name' => $request->name,
                'file' => $filename,
            ]);
        }

        $investment = Investment::findOrFail($investmentId);
        $emailResult = null;
        if ((int) $investment->status === 1 && !$investment->agreement_email_sent_at) {
            $emailResult = app(AgreementEmailService::class)->sendInvestmentAgreement($investment);
        }

        $message = 'Documents Uploaded Successfully';
        if ($emailResult) {
            $message .= $emailResult['sent'] ? '. '.$emailResult['message'] : '. '.$emailResult['message'];
        }

        return response()->json(['result' => 'success', 'message' => $message]);
    }
}
