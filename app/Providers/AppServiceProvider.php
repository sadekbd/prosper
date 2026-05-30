<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // ── Force HTTPS in production ──────────────────────────
        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        // ── Global view data ───────────────────────────────────
        // Share flat key→value settings with ALL views
        // (used by header, footer, contact page for dynamic info)
        View::composer('*', function ($view) {
            try {
                $view->with('settings', SiteSetting::getAllSettings());
            } catch (\Exception $e) {
                // Gracefully handle if DB not yet migrated
                $view->with('settings', []);
            }
        });

        // ── Pagination views ───────────────────────────────────
        // Public pages use our custom tailwind paginator
        Paginator::defaultView('vendor.pagination.tailwind');
        Paginator::defaultSimpleView('vendor.pagination.simple-tailwind');
    }
}