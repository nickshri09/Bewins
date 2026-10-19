<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewWithdrowRequest extends Model
{
    use HasFactory;
    protected $table = 'new_withdrawrequest';
    protected $fillable = [
        'user_id',
        'coin_transfer',
        'holder_name',
        'bank_name',
        'account_number',
        'ifsc_code',
        'account_type',
        'status',
        'remark',
    ];

}
