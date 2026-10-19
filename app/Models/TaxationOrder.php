<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\SerialNumberGenerator;

class TaxationOrder extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (TaxationOrder $order) {
            if (blank($order->tax_code)) {
                $order->tax_code = SerialNumberGenerator::next('BTX');
            }
        });
    }

    protected $fillable = [
        'tax_code',
        'user_id',
        'member_id',
        'service_name',
        'service_fee',
        'scope_service_name',
        'scope_fee',
    ];
}
