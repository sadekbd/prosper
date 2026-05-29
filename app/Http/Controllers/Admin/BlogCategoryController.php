<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::withCount(['articles as total_articles'])
            ->withCount(['articles as published_articles' => fn($q) => $q->where('status','published')])
            ->orderBy('sort_order')
            ->get();

        return view('admin.blog.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => ['required','string','max:100'],
            'description'=> ['nullable','string','max:300'],
            'color'      => ['required','string','size:7'],
            'sort_order' => ['nullable','integer','min:0'],
        ]);

        $slug = Str::slug($validated['name']);
        $count = BlogCategory::where('slug','like',"{$slug}%")->count();

        BlogCategory::create([
            'name'        => $validated['name'],
            'slug'        => $count ? "{$slug}-{$count}" : $slug,
            'description' => $validated['description'],
            'color'       => $validated['color'],
            'sort_order'  => $validated['sort_order'] ?? 0,
            'status'      => 'active',
        ]);

        return redirect()->route('admin.blog.categories')->with('success', 'Category created.');
    }

    public function edit(int $id)
    {
        $category = BlogCategory::findOrFail($id);
        $categories = BlogCategory::withCount('articles')->orderBy('sort_order')->get();
        return view('admin.blog.categories.index', compact('categories', 'category'));
    }

    public function update(Request $request, int $id)
    {
        $category  = BlogCategory::findOrFail($id);
        $validated = $request->validate([
            'name'       => ['required','string','max:100'],
            'description'=> ['nullable','string','max:300'],
            'color'      => ['required','string','size:7'],
            'sort_order' => ['nullable','integer','min:0'],
        ]);

        $category->update([
            'name'        => $validated['name'],
            'description' => $validated['description'],
            'color'       => $validated['color'],
            'sort_order'  => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('admin.blog.categories')->with('success', 'Category updated.');
    }

    public function destroy(int $id)
    {
        $category = BlogCategory::withCount('articles')->findOrFail($id);
        if ($category->articles_count > 0) {
            return back()->with('error', "Cannot delete — {$category->articles_count} article(s) use this category.");
        }
        $category->delete();
        return redirect()->route('admin.blog.categories')->with('success', 'Category deleted.');
    }
}