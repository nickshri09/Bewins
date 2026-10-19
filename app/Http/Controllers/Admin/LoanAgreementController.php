<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoanAgreementDocument;
use App\Models\Loan;
use App\Services\AgreementEmailService;

class LoanAgreementController extends Controller
{
    public function create($loanId)
    {
       // dd("hdgfgsdjkfhd");
        return view('backend.loan.agreement_model', compact('loanId'));

    }

    public function store(Request $request, $loanId)
    {
       // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png,docx'
        ]);

        foreach ($request->file('documents') as $file) {
            $filename = time() . rand(1000,9999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/agreements'), $filename);

            LoanAgreementDocument::create([
                'loan_id' => $loanId,
                'name'    => $request->name,
                'file'    => $filename
            ]);
        }

        $loan = Loan::findOrFail($loanId);
        $emailResult = null;
        if ((int) $loan->status === 1 && !$loan->agreement_email_sent_at) {
            $emailResult = app(AgreementEmailService::class)->sendLoanAgreement($loan);
        }

        $message = 'Documents Uploaded Successfully';
        if ($emailResult) {
            $message .= '. '.$emailResult['message'];
        }

        return response()->json(['result' => 'success', 'message' => $message]);
    }

}

