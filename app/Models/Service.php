<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'unique_id',
        'service_name',
        'fee',
        'batch_id',
        'obligation',
        'frequency',
        'maximum_tat',
        'product_category',
        'main_service_id',
        'main_service_name',
    ];

    public function mainService()
    {
        return $this->belongsTo(MainService::class);
    }


}
