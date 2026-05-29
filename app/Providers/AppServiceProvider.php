<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Share site settings with ALL views (header, footer, pages)
        View::composer('*', function ($view) {
            $view->with('settings', SiteSetting::getAllSettings());
        });
    }
}