<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'year',
        'amount',
        'status',
        'payment_date',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
