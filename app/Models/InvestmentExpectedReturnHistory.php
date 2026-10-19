<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentExpectedReturnHistory extends Model
{
    protected $fillable = [
        'expected_return_id',
        'old_value',
        'new_value',
        'changed_by'
    ];
}

