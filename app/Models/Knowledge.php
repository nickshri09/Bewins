<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Knowledge extends Model  // ← 'Knowledge' (capital K)
{
    protected $table = 'knowledge';

    protected $fillable = [
        'title',
        'description',
        'category',
        'is_active',
    ];

    /**
     * Get all images associated with this knowledge entry.
     */
    public function images()
    {
        return $this->hasMany(KnowledgeImage::class, 'knowledge_id');
    }
}