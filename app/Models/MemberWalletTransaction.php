<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberWalletTransaction extends Model
{
    protected $fillable = [
        'member_wallet_id',
        'member_id',
        'investment_id',
        'investment_repayment_id',
        'order_id',
        'type',
        'gross_amount',
        'tds_amount',
        'amount',
        'days_count',
        'posted_from',
        'posted_to',
        'note',
        'created_by',
    ];

    public function wallet()
    {
        return $this->belongsTo(MemberWallet::class, 'member_wallet_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
