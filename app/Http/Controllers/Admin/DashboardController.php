<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\BlogArticle;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\PortfolioProject;
use App\Models\Service;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth('admin')->user();

        // ── Stats ──────────────────────────────────────────────
        $stats = [
            'total_articles'     => 0,
            'published_articles' => 0,
            'draft_articles'     => 0,
            'review_articles'    => 0,
            'total_portfolio'    => 0,
            'total_messages'     => 0,
            'new_messages'       => 0,
            'active_services'    => 0,
            'total_writers'      => 0,
            'subscribers'        => 0,
        ];

        if (Schema::hasTable('blog_articles')) {
            $stats['total_articles']     = BlogArticle::count();
            $stats['published_articles'] = BlogArticle::where('status', 'published')->count();
            $stats['draft_articles']     = BlogArticle::where('status', 'draft')->count();
            $stats['review_articles']    = BlogArticle::where('status', 'review')->count();
        }

        if (Schema::hasTable('portfolio_projects')) {
            $stats['total_portfolio'] = PortfolioProject::where('status', 'published')->count();
        }

        if (Schema::hasTable('contact_messages')) {
            $stats['total_messages'] = ContactMessage::count();
            $stats['new_messages']   = ContactMessage::where('status', 'new')->count();
        }

        if (Schema::hasTable('services')) {
            $stats['active_services'] = Service::where('status', 'active')->count();
        }

        if (Schema::hasTable('admin_users')) {
            $stats['total_writers'] = AdminUser::where('role', 'article_writer')->count();
        }

        if (Schema::hasTable('newsletter_subscribers')) {
            $stats['subscribers'] = NewsletterSubscriber::where('status', 'active')->count();
        }

        // ── Recent data ───────────────────────────────────────
        $recentMessages = collect();
        if (Schema::hasTable('contact_messages')) {
            $recentMessages = ContactMessage::latest()->take(5)->get();
        }

        // Article writers see only their own articles
        $recentArticles = collect();
        if (Schema::hasTable('blog_articles')) {
            $query = BlogArticle::with('category')->latest();
            if ($user->role === 'article_writer') {
                $query->where('author_id', $user->id);
            }
            $recentArticles = $query->take(5)->get();
        }

        return view('admin.dashboard', compact('stats', 'recentMessages', 'recentArticles'));
    }
}