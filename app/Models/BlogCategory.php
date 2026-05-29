<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogCategory extends Model
{
    protected $table    = 'blog_categories';
    protected $fillable = ['name', 'slug', 'description', 'color', 'sort_order', 'status'];

    public function articles(): HasMany
    {
        return $this->hasMany(BlogArticle::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->orderBy('sort_order');
    }

    public function getPublishedCountAttribute(): int
    {
        return $this->articles()->where('status', 'published')->count();
    }
}