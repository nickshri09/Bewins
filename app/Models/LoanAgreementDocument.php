<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanAgreementDocument extends Model
{
    use HasFactory;
    protected $table="loan_agreement_documents";
    protected $fillable = [
        'loan_id',
        'name',
        'file',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

}
