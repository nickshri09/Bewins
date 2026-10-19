<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainService extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_code',
        'name',
        'amount',
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
