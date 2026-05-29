<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ── Admin middleware aliases ───────────────────────────
        $middleware->alias([
            'admin.auth'   => \App\Http\Middleware\AdminAuth::class,
            'admin.super'  => \App\Http\Middleware\SuperAdminOnly::class,
            'admin.admin'  => \App\Http\Middleware\AdminOrAbove::class,
            'admin.writer' => \App\Http\Middleware\ArticleWriterAccess::class,
            'admin.guest'  => \App\Http\Middleware\RedirectIfAdminAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();