<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeImage extends Model
{
    protected $table = 'knowledge_images';

    protected $fillable = [
        'knowledge_id',
        'path',
    ];

    public function knowledge()
    {
        return $this->belongsTo(Knowledge::class); // यहाँ भी 'Knowledge' (capital K)
    }
}