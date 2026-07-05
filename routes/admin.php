<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\SignupController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BlogArticleController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\PortfolioController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\HomepageController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {

    // ── Guest Auth ────────────────────────────────────────────
    Route::middleware('admin.guest')->group(function () {
        Route::get( '/login',           [LoginController::class,          'showForm'])->name('login');
        Route::post('/login',           [LoginController::class,          'login'])->name('login.post');
        Route::get( '/signup',          [SignupController::class,         'showForm'])->name('signup');
        Route::post('/signup',          [SignupController::class,         'register'])->name('signup.post');
        Route::get( '/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('forgot');
        Route::post('/forgot-password', [ForgotPasswordController::class, 'verify'])->name('forgot.post');
        Route::get( '/reset-password',  [ForgotPasswordController::class, 'showResetForm'])->name('reset.form');
        Route::post('/reset-password',  [ForgotPasswordController::class, 'reset'])->name('reset.post');
    });

    // ── Authenticated ─────────────────────────────────────────
    Route::middleware(['admin.auth'])->group(function () {

        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Homepage content (admin+)
        Route::middleware('admin.admin')->prefix('homepage')->group(function () {
            Route::get('/', [HomepageController::class, 'edit'])->name('homepage.edit');
            Route::put('/', [HomepageController::class, 'update'])->name('homepage.update');
        });

        // Blog Articles (all roles)
        Route::prefix('blog/articles')->group(function () {
            Route::get('/',                [BlogArticleController::class, 'index'])->name('blog.articles');
            Route::get('/create',          [BlogArticleController::class, 'create'])->name('blog.articles.create');
            Route::post('/',               [BlogArticleController::class, 'store'])->name('blog.articles.store');
            Route::get('/{id}/edit',       [BlogArticleController::class, 'edit'])->name('blog.articles.edit');
            Route::put('/{id}',            [BlogArticleController::class, 'update'])->name('blog.articles.update');
            Route::patch('/{id}/publish',  [BlogArticleController::class, 'publish'])->name('blog.articles.publish');
            Route::patch('/{id}/reject',   [BlogArticleController::class, 'reject'])->name('blog.articles.reject');
            Route::delete('/{id}',         [BlogArticleController::class, 'destroy'])->name('blog.articles.destroy');
        });

        // Blog Categories (admin+)
        Route::middleware('admin.admin')->prefix('blog/categories')->group(function () {
            Route::get('/',        [BlogCategoryController::class, 'index'])->name('blog.categories');
            Route::post('/',       [BlogCategoryController::class, 'store'])->name('blog.categories.store');
            Route::get('/{id}/edit',[BlogCategoryController::class, 'edit'])->name('blog.categories.edit');
            Route::put('/{id}',    [BlogCategoryController::class, 'update'])->name('blog.categories.update');
            Route::delete('/{id}', [BlogCategoryController::class, 'destroy'])->name('blog.categories.destroy');
        });

        // Services (admin+)
        Route::middleware('admin.admin')->prefix('services')->group(function () {
            Route::get('/',         [ServiceController::class, 'index'])->name('services');
            Route::get('/create',   [ServiceController::class, 'create'])->name('services.create');
            Route::post('/',        [ServiceController::class, 'store'])->name('services.store');
            Route::get('/{id}/edit',[ServiceController::class, 'edit'])->name('services.edit');
            Route::put('/{id}',     [ServiceController::class, 'update'])->name('services.update');
            Route::delete('/{id}',  [ServiceController::class, 'destroy'])->name('services.destroy');
        });

        // Portfolio (admin+)
        Route::middleware('admin.admin')->prefix('portfolio')->group(function () {
            Route::get('/',         [PortfolioController::class, 'index'])->name('portfolio');
            Route::get('/create',   [PortfolioController::class, 'create'])->name('portfolio.create');
            Route::post('/',        [PortfolioController::class, 'store'])->name('portfolio.store');
            Route::get('/{id}/edit',[PortfolioController::class, 'edit'])->name('portfolio.edit');
            Route::put('/{id}',     [PortfolioController::class, 'update'])->name('portfolio.update');
            Route::delete('/{id}',  [PortfolioController::class, 'destroy'])->name('portfolio.destroy');
        });

        // Contact Messages (admin+)
        Route::middleware('admin.admin')->prefix('messages')->group(function () {
            Route::get('/',              [ContactMessageController::class, 'index'])->name('messages');
            Route::get('/{id}',          [ContactMessageController::class, 'show'])->name('messages.show');
            Route::patch('/{id}/status', [ContactMessageController::class, 'updateStatus'])->name('messages.status');
            Route::delete('/{id}',       [ContactMessageController::class, 'destroy'])->name('messages.destroy');
        });

        // Newsletter (admin+)
        Route::middleware('admin.admin')->prefix('newsletter')->group(function () {
            Route::get('/',       [NewsletterController::class, 'index'])->name('newsletter');
            Route::delete('/{id}',[NewsletterController::class, 'destroy'])->name('newsletter.destroy');
        });

        // Users (super only)
        Route::middleware('admin.super')->prefix('users')->group(function () {
            Route::get('/',              [UserController::class, 'index'])->name('users');
            Route::patch('/{id}/status', [UserController::class, 'updateStatus'])->name('users.status');
            Route::patch('/{id}/role',   [UserController::class, 'updateRole'])->name('users.role');
            Route::delete('/{id}',       [UserController::class, 'destroy'])->name('users.destroy');
        });

        // Settings (super only)
        Route::middleware('admin.super')->prefix('settings')->group(function () {
            Route::get('/',  [SiteSettingController::class, 'index'])->name('settings');
            Route::post('/', [SiteSettingController::class, 'update'])->name('settings.update');
        });
    });
});
