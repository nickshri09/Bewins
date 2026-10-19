<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionRecord extends Model
{
    protected $fillable = [
        'investment_id', 'source_member_id', 'recipient_type', 'recipient_id',
        'level', 'base_amount', 'rate_percent', 'amount',
    ];

    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }

    public function sourceMember()
    {
        return $this->belongsTo(Member::class, 'source_member_id')->withoutGlobalScopes();
    }
}
