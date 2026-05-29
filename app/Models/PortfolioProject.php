<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PortfolioProject extends Model
{
    protected $table    = 'portfolio_projects';
    protected $fillable = [
        'title', 'slug', 'category', 'short_description', 'full_description',
        'technologies', 'result_summary', 'client_name', 'project_url',
        'featured_image', 'gallery_images', 'meta_title', 'meta_description',
        'is_featured', 'sort_order', 'status',
    ];

    protected $casts = [
        'technologies'   => 'array',
        'gallery_images' => 'array',
        'is_featured'    => 'boolean',
    ];

    // ── Scopes ──────────────────────────────────────────────────
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // ── Helpers ─────────────────────────────────────────────────
    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'google_ads'      => 'Google Ads',
            'tracking_setup'  => 'Tracking Setup',
            'web_development' => 'Web Development',
            'landing_page'    => 'Landing Page',
            'automation'      => 'Automation',
            default           => ucfirst($this->category),
        };
    }
}