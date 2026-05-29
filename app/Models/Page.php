<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table    = 'pages';
    protected $fillable = [
        'title', 'slug', 'content', 'meta_title',
        'meta_description', 'og_title', 'og_image', 'is_system', 'status',
    ];

    protected $casts = ['is_system' => 'boolean'];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}