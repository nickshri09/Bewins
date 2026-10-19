<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'member_id',
        'main_service_name',
        'main_service_fee',
        'scope_service_name',
        'scope_service_fee',
        'scope_service_id',
        'status',
        'lead_status',
        'order_status',
        'razorpay_order_id',
        'razorpay_payment_id',
        'payment_status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'scope_service_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
