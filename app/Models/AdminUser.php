<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AdminUser extends Authenticatable
{
    use Notifiable;

    protected $table    = 'admin_users';
    protected $fillable = [
        'username', 'full_name', 'email', 'mobile', 'password',
        'role', 'status', 'profile_image', 'last_login_at', 'last_login_ip',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'last_login_at' => 'datetime',
        'password'      => 'hashed',
    ];

    // ── Role helpers ─────────────────────────────────────────────
    public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }
    public function isAdmin(): bool      { return in_array($this->role, ['super_admin', 'admin']); }
    public function isWriter(): bool     { return $this->role === 'article_writer'; }
    public function isActive(): bool     { return $this->status === 'active'; }

    // ── Relationships ────────────────────────────────────────────
    public function articles()
    {
        return $this->hasMany(BlogArticle::class, 'author_id');
    }
}