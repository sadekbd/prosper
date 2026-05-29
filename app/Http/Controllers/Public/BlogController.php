<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BlogArticle;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $categorySlug = $request->query('category', null);
        $search       = $request->query('search', null);
        $articles     = collect();
        $categories   = collect();
        $activeCategory = null;

        if (Schema::hasTable('blog_articles')) {
            $categories = BlogCategory::active()->withCount([
                'articles as published_count' => fn($q) => $q->where('status', 'published'),
            ])->get();

            $query = BlogArticle::with('category', 'author')
                ->published();

            // Filter by category
            if ($categorySlug) {
                $activeCategory = $categories->firstWhere('slug', $categorySlug);
                if ($activeCategory) {
                    $query->where('category_id', $activeCategory->id);
                }
            }

            // Search filter
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('excerpt', 'like', "%{$search}%");
                });
            }

            $articles = $query->paginate(9)->withQueryString();

            // Popular posts for sidebar
            $popularPosts = BlogArticle::with('category')
                ->where('status', 'published')
                ->orderBy('views', 'desc')
                ->take(5)
                ->get();
        } else {
            $popularPosts = collect();
        }

        return view('public.blog', compact(
            'articles', 'categories', 'activeCategory', 'search', 'popularPosts'
        ));
    }

    public function show(string $slug)
    {
        $article = BlogArticle::with('category', 'author')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Increment view count
        $article->incrementViews();

        // Related articles — same category
        $related = BlogArticle::with('category')
            ->where('status', 'published')
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->take(3)
            ->get();

        return view('public.blog-single', compact('article', 'related'));
    }

    public function category(string $slug)
    {
        return redirect()->route('blog', ['category' => $slug]);
    }
}