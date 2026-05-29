<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogArticle;
use App\Models\BlogCategory;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogArticleController extends Controller
{
    public function index(Request $request)
    {
        $user   = auth('admin')->user();
        $status = $request->query('status', 'all');
        $search = $request->query('search', '');

        $query = BlogArticle::with('category', 'author')->latest();

        // Article writers see only their own articles
        if ($user->role === 'article_writer') {
            $query->where('author_id', $user->id);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        $articles = $query->paginate(15)->withQueryString();

        return view('admin.blog.articles.index', compact('articles', 'status', 'search'));
    }

    public function create()
    {
        $categories = BlogCategory::active()->get();
        return view('admin.blog.articles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $user = auth('admin')->user();

        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:250'],
            'category_id'      => ['required', 'exists:blog_categories,id'],
            'excerpt'          => ['nullable', 'string', 'max:500'],
            'content'          => ['required', 'string', 'min:50'],
            'featured_image'   => ['nullable', 'image', 'max:2048'],
            'meta_title'       => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'tags'             => ['nullable', 'string'],
            'read_time'        => ['nullable', 'integer', 'min:1', 'max:120'],
        ]);

        // Generate unique slug
        $slug = $this->generateSlug($validated['title']);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $imagePath = $request->file('featured_image')->store('uploads/blog', 'public');
        }

        // Parse tags from comma-separated string
        $tags = null;
        if (!empty($validated['tags'])) {
            $tags = array_map('trim', explode(',', $validated['tags']));
        }

        // Writers submit for review; admins can set any status
        $status = 'draft';
        if ($request->input('action') === 'submit') {
            $status = $user->isAdmin() ? 'published' : 'review';
        }

        $article = BlogArticle::create([
            'author_id'        => $user->id,
            'category_id'      => $validated['category_id'],
            'title'            => $validated['title'],
            'slug'             => $slug,
            'excerpt'          => $validated['excerpt'],
            'content'          => $validated['content'],
            'featured_image'   => $imagePath,
            'meta_title'       => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'tags'             => $tags,
            'read_time'        => $validated['read_time'],
            'status'           => $status,
            'published_at'     => $status === 'published' ? now() : null,
        ]);

        ActivityLog::log('created_article', 'BlogArticle', $article->id, "Created: {$article->title}");

        $message = match($status) {
            'published' => 'Article published successfully.',
            'review'    => 'Article submitted for review.',
            default     => 'Article saved as draft.',
        };

        return redirect()->route('admin.blog.articles')->with('success', $message);
    }

    public function edit(int $id)
    {
        $article = $this->findArticleOrFail($id);
        $categories = BlogCategory::active()->get();
        return view('admin.blog.articles.edit', compact('article', 'categories'));
    }

    public function update(Request $request, int $id)
    {
        $article = $this->findArticleOrFail($id);
        $user    = auth('admin')->user();

        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:250'],
            'category_id'      => ['required', 'exists:blog_categories,id'],
            'excerpt'          => ['nullable', 'string', 'max:500'],
            'content'          => ['required', 'string', 'min:50'],
            'featured_image'   => ['nullable', 'image', 'max:2048'],
            'meta_title'       => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'tags'             => ['nullable', 'string'],
            'read_time'        => ['nullable', 'integer', 'min:1', 'max:120'],
        ]);

        // Handle image
        if ($request->hasFile('featured_image')) {
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')
                ->store('uploads/blog', 'public');
        }

        // Tags
        $tags = null;
        if (!empty($validated['tags'])) {
            $tags = array_map('trim', explode(',', $validated['tags']));
        }

        // Status
        $status = $article->status;
        if ($request->input('action') === 'submit') {
            $status = $user->isAdmin() ? 'published' : 'review';
        } elseif ($request->input('action') === 'draft') {
            $status = 'draft';
        }

        $article->update([
            'category_id'      => $validated['category_id'],
            'title'            => $validated['title'],
            'excerpt'          => $validated['excerpt'],
            'content'          => $validated['content'],
            'featured_image'   => $validated['featured_image'] ?? $article->featured_image,
            'meta_title'       => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'tags'             => $tags,
            'read_time'        => $validated['read_time'],
            'status'           => $status,
            'published_at'     => $status === 'published' && !$article->published_at ? now() : $article->published_at,
        ]);

        ActivityLog::log('updated_article', 'BlogArticle', $article->id, "Updated: {$article->title}");

        return redirect()->route('admin.blog.articles')->with('success', 'Article updated successfully.');
    }

    public function publish(int $id)
    {
        $this->requireAdminRole();
        $article = BlogArticle::findOrFail($id);
        $article->update(['status' => 'published', 'published_at' => now()]);
        ActivityLog::log('published_article', 'BlogArticle', $article->id);
        return back()->with('success', "Article \"{$article->title}\" published.");
    }

    public function reject(int $id)
    {
        $this->requireAdminRole();
        $article = BlogArticle::findOrFail($id);
        $article->update(['status' => 'rejected']);
        return back()->with('success', 'Article rejected and returned to writer.');
    }

    public function destroy(int $id)
    {
        $article = $this->findArticleOrFail($id);
        if ($article->featured_image) {
            Storage::disk('public')->delete($article->featured_image);
        }
        ActivityLog::log('deleted_article', 'BlogArticle', $id, "Deleted: {$article->title}");
        $article->delete();
        return redirect()->route('admin.blog.articles')->with('success', 'Article deleted.');
    }

    // ── Private helpers ───────────────────────────────────────

    private function findArticleOrFail(int $id): BlogArticle
    {
        $user    = auth('admin')->user();
        $article = BlogArticle::findOrFail($id);

        // Writers can only edit their own articles
        if ($user->role === 'article_writer' && $article->author_id !== $user->id) {
            abort(403, 'You can only edit your own articles.');
        }

        return $article;
    }

    private function requireAdminRole(): void
    {
        if (!auth('admin')->user()->isAdmin()) {
            abort(403, 'Admin access required.');
        }
    }

    private function generateSlug(string $title): string
    {
        $slug  = Str::slug($title);
        $count = BlogArticle::where('slug', 'like', "{$slug}%")->count();
        return $count ? "{$slug}-{$count}" : $slug;
    }
}