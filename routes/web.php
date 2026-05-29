<?php

use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\AboutController;
use App\Http\Controllers\Public\ServicesController;
use App\Http\Controllers\Public\PortfolioController;
use App\Http\Controllers\Public\BlogController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\NewsletterController;
use App\Http\Controllers\Public\PageController;
use Illuminate\Support\Facades\Route;

// ── Public pages ───────────────────────────────────────────────────
Route::get('/',        [HomeController::class,     'index'])->name('home');
Route::get('/about',   [AboutController::class,    'index'])->name('about');

// Services
Route::get('/services',        [ServicesController::class, 'index'])->name('services');
Route::get('/services/{slug}', [ServicesController::class, 'show'])->name('services.show');

// Portfolio
Route::get('/portfolio',         [PortfolioController::class, 'index'])->name('portfolio');
Route::get('/portfolio/{slug}',  [PortfolioController::class, 'show'])->name('portfolio.show');

// Blog
Route::get('/blog',                     [BlogController::class, 'index'])->name('blog');
Route::get('/blog/category/{slug}',     [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/{slug}',              [BlogController::class, 'show'])->name('blog.show');

// Contact
Route::get('/contact',  [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->name('newsletter.subscribe');

// Static pages
Route::get('/privacy-policy', fn() => view('public.privacy-policy'))->name('privacy-policy');

// ── Admin routes ───────────────────────────────────────────────────
require __DIR__.'/admin.php';