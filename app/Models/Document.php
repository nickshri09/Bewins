<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
  

      protected $fillable = ['order_id', 'file_path', 'name'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}


