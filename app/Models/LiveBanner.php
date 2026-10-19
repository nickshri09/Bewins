<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveBanner extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'image',
        'button_text',
        'button_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
