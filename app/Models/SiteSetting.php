<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $table    = 'site_settings';
    protected $fillable = ['key', 'value', 'group', 'label', 'type'];

    /** Returns all settings as key => value array (cached 60 min) */
    public static function getAllSettings(): array
    {
        return Cache::remember('site_settings_all', 3600, function () {
            return static::pluck('value', 'key')->toArray();
        });
    }

    /** Get single setting value */
    public static function get(string $key, mixed $default = null): mixed
    {
        return static::getAllSettings()[$key] ?? $default;
    }

    /** Clear settings cache after updates */
    public static function clearCache(): void
    {
        Cache::forget('site_settings_all');
    }
}