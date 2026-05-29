<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\PortfolioProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PortfolioController extends Controller
{
    /** Valid filter categories */
    private const CATEGORIES = [
        'google_ads', 'tracking_setup', 'web_development', 'landing_page', 'automation',
    ];

    public function index(Request $request)
    {
        $category = $request->query('category', 'all');
        $portfolioProjects = collect();

        if (Schema::hasTable('portfolio_projects')) {
            $query = PortfolioProject::published()
                ->orderBy('is_featured', 'desc')
                ->orderBy('sort_order', 'asc');

            if ($category !== 'all' && in_array($category, self::CATEGORIES)) {
                $query->where('category', $category);
            }

            $portfolioProjects = $query->get();
        }

        return view('public.portfolio', compact('portfolioProjects', 'category'));
    }

    public function show(string $slug)
    {
        $project = PortfolioProject::published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Related projects — same category, excluding current
        $related = PortfolioProject::published()
            ->where('category', $project->category)
            ->where('id', '!=', $project->id)
            ->take(3)
            ->get();

        return view('public.portfolio-single', compact('project', 'related'));
    }
}