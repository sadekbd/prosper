<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BlogArticle;
use App\Models\BlogCategory;
use App\Models\PortfolioProject;
use App\Models\Service;

class SitemapController extends Controller
{
    public function index()
    {
        // ── Static pages ───────────────────────────────────────
        $staticPages = [
            ['url' => route('home'),          'priority' => '1.0', 'changefreq' => 'weekly',  'lastmod' => now()->toDateString()],
            ['url' => route('about'),         'priority' => '0.8', 'changefreq' => 'monthly', 'lastmod' => now()->toDateString()],
            ['url' => route('services'),      'priority' => '0.9', 'changefreq' => 'monthly', 'lastmod' => now()->toDateString()],
            ['url' => route('portfolio'),     'priority' => '0.8', 'changefreq' => 'weekly',  'lastmod' => now()->toDateString()],
            ['url' => route('blog'),          'priority' => '0.9', 'changefreq' => 'daily',   'lastmod' => now()->toDateString()],
            ['url' => route('contact'),       'priority' => '0.7', 'changefreq' => 'monthly', 'lastmod' => now()->toDateString()],
            ['url' => route('privacy-policy'),'priority' => '0.3', 'changefreq' => 'yearly',  'lastmod' => now()->toDateString()],
        ];

        // ── Dynamic content ────────────────────────────────────
        $services  = Service::active()->get();
        $portfolio = PortfolioProject::published()->orderBy('updated_at', 'desc')->get();
        $articles  = BlogArticle::published()->orderBy('published_at', 'desc')->get();
        $categories= BlogCategory::active()->get();

        return response()
            ->view('sitemap', compact('staticPages', 'services', 'portfolio', 'articles', 'categories'))
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}