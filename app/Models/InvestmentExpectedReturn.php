<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentExpectedReturn extends Model
{
    protected $table = 'investment_expected_returns';

    protected $fillable = [
        'type',
        'expected_return',
        'status'
    ];

    public function histories()
    {
        return $this->hasMany(InvestmentExpectedReturnHistory::class, 'expected_return_id');
    }
}
