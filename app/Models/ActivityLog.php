<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public    $timestamps  = false;
    protected $table       = 'activity_logs';
    protected $fillable    = [
        'user_id', 'action', 'model_type', 'model_id', 'description', 'ip_address',
    ];

    protected $casts = ['created_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'user_id');
    }

    /** Quick log helper — call anywhere: ActivityLog::log('published_article', ...) */
    public static function log(
        string  $action,
        ?string $modelType  = null,
        ?int    $modelId    = null,
        ?string $description = null
    ): void {
        static::create([
            'user_id'     => auth('admin')->id(),
            'action'      => $action,
            'model_type'  => $modelType,
            'model_id'    => $modelId,
            'description' => $description,
            'ip_address'  => request()->ip(),
            'created_at'  => now(),
        ]);
    }
}