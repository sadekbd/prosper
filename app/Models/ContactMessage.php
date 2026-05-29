<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $table    = 'contact_messages';
    protected $fillable = [
        'name','email','mobile','website_url',
        'service_interest','message','ip_address',
        'user_agent','status','admin_notes','replied_at',
    ];

    protected $casts = ['replied_at' => 'datetime'];
}