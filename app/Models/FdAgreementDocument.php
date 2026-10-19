<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FdAgreementDocument extends Model
{
    protected $table = 'fd_agreement_documents';

    protected $fillable = [
        'fd_id',
        'name',
        'file'
    ];

    public function fd()
    {
        return $this->belongsTo(Fd::class, 'fd_id');
    }
}
