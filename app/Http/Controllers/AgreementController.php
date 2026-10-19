<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AgreementController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $data = [
            'name' => $user->name ?? 'Demo User',
            'date' => now()->format('d-m-Y'),
            'id'   => $user->id ?? 1,
            'number' =>$user->mobile_number ?? '1234567890',
            
            'last_name' =>$user->last_name,
            'aadhar' => $user->aadhar_number ?? '209079584483',
            'pan' => $user->pan_number ?? 'CHIPR9131E',
            'address' => $user->address ?? 'JAWAHI DIYAR PO.BALLUA BUXAR BIHAR MO 8252042652, Buxar, Bihar: 802130, India',
            'residence_address' => $user->residence_address ?? 'Dadan prasad jawahi diyar, Balua, Buxar, Bihar 802130, Buxar, Bihar: 802130, India',
        ];

        $pdf = Pdf::loadView('pdf.agreement', $data);
        return $pdf->stream('agreement.pdf');
    }
    
    
    public function showCertificate()
    {
        $user = auth()->user();
        $data = [
            'name' => $user->name ?? 'Demo User',
            'number' =>$user->mobile_number ?? '1234567890',
            'profileId' => $user->user_id ?? 'N/A',
            
            'last_name' =>$user->last_name,
            'date' => now()->format('d-m-Y'),
            'id'   => $user->id ?? 1,
            'aadhar' => $user->aadhar_number ?? '209079584483',
              ];

       // Keep certificate assets local. Remote fetching can make Dompdf call
       // back into the single-threaded artisan server and time out.
       $pdf = Pdf::loadView('pdf.certificate', $data)
          ->setPaper('A4', 'landscape');
        return $pdf->stream('certificate.pdf');
    }
    
    
    
    public function showLoanAgreement()
    {
        $user = auth()->user();
        $data = [
            'name' => $user->name ?? 'Demo User',
            'number' =>$user->mobile_number ?? '1234567890',
            'pan' => $user->pan_number ?? 'CHIPR9131E',
            'last_name' =>$user->last_name,
            'date' => now()->format('d-m-Y'),
            'id'   => $user->id ?? 1,
            'aadhar' => $user->aadhar_number ?? '209079584483',
              ];

         $pdf = Pdf::loadView('pdf.loan_agreement', $data);
        return $pdf->stream('loan_agreement.pdf');
    }
}
