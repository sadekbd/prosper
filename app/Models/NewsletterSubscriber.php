<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    protected $table    = 'newsletter_subscribers';
    protected $fillable = ['email', 'name', 'status', 'ip_address', 'subscribed_at', 'unsubscribed_at'];

    protected $casts = [
        'subscribed_at'   => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}