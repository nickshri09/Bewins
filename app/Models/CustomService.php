<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomService extends Model
{
       use HasFactory;

   public function mainService()
    {
        return $this->belongsTo(MainService::class, 'main_service_id');
    }
      protected $fillable = [
        'custom_service_name',
        'main_service_id',
        'category',
        'sub_category',
        'obligation',
        'frequency',
        'maximum_tat'
    ];
}


