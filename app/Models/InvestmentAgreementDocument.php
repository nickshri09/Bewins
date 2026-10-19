<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentAgreementDocument extends Model
{
    use HasFactory;

    protected $table = "investment_agreement_documents";

    protected $fillable = [
        'investment_id',
        'name',
        'file',
    ];

    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }
}
