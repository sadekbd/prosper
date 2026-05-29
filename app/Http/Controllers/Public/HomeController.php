<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\PortfolioProject;
use App\Models\BlogArticle;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        // Services — always safe (table exists from Phase 1)
        $services = Service::active()->take(3)->get();

        // Portfolio — guard in case table doesn't exist yet
        $portfolios = collect();
        if (Schema::hasTable('portfolio_projects')) {
            $portfolios = PortfolioProject::published()
                ->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
        }

        // Blog articles — guard in case table doesn't exist yet
        $articles = collect();
        if (Schema::hasTable('blog_articles')) {
            $articles = BlogArticle::with('category')
                ->published()
                ->take(3)
                ->get();
        }

        return view('public.home', compact('services', 'portfolios', 'articles'));
    }
}