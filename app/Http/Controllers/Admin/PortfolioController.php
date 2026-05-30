<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioProject;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PortfolioController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category', 'all');
        $status   = $request->query('status', 'all');

        $query = PortfolioProject::orderBy('is_featured', 'desc')->orderBy('sort_order');

        if ($category !== 'all') $query->where('category', $category);
        if ($status   !== 'all') $query->where('status',   $status);

        $projects = $query->paginate(12)->withQueryString();

        return view('admin.portfolio.index', compact('projects', 'category', 'status'));
    }

    public function create()
    {
        return view('admin.portfolio.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'             => ['required','string','max:200'],
            'category'          => ['required','in:google_ads,tracking_setup,web_development,landing_page,automation'],
            'short_description' => ['required','string','max:500'],
            'full_description'  => ['nullable','string'],
            'technologies'      => ['nullable','string'],
            'result_summary'    => ['nullable','string','max:400'],
            'client_name'       => ['nullable','string','max:150'],
            'project_url'       => ['nullable','url','max:255'],
            'featured_image'    => ['nullable','image','max:3072'],
            'meta_title'        => ['nullable','string','max:200'],
            'meta_description'  => ['nullable','string','max:300'],
            'is_featured'       => ['nullable','boolean'],
            'sort_order'        => ['nullable','integer'],
            'status'            => ['required','in:published,draft'],
        ]);

        $slug  = Str::slug($validated['title']);
        $count = PortfolioProject::where('slug','like',"{$slug}%")->count();

        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $imagePath = $request->file('featured_image')->store('uploads/portfolio', 'public');
        }

        $technologies = null;
        if (!empty($validated['technologies'])) {
            $technologies = array_map('trim', explode(',', $validated['technologies']));
        }

        PortfolioProject::create([
    'title'             => $validated['title'],
    'slug'              => $count ? "{$slug}-{$count}" : $slug,
    'category'          => $validated['category'],
    'short_description' => $validated['short_description'],
    'full_description'  => $validated['full_description']  ?? null,
    'technologies'      => $technologies,
    'result_summary'    => $validated['result_summary']    ?? null,
    'client_name'       => $validated['client_name']       ?? null,
    'project_url'       => $validated['project_url']       ?? null,
    'featured_image'    => $imagePath,
    'meta_title'        => $validated['meta_title']        ?? null,
    'meta_description'  => $validated['meta_description']  ?? null,
    'is_featured'       => $request->boolean('is_featured'),
    'sort_order'        => $validated['sort_order']        ?? 0,
    'status'            => $validated['status'],
]);

        ActivityLog::log('created_project', 'PortfolioProject', null, "Created: {$validated['title']}");
        return redirect()->route('admin.portfolio')->with('success', 'Project created successfully.');
    }

    public function edit(int $id)
    {
        $project = PortfolioProject::findOrFail($id);
        return view('admin.portfolio.edit', compact('project'));
    }

    public function update(Request $request, int $id)
    {
        $project   = PortfolioProject::findOrFail($id);
        $validated = $request->validate([
            'title'             => ['required','string','max:200'],
            'category'          => ['required','in:google_ads,tracking_setup,web_development,landing_page,automation'],
            'short_description' => ['required','string','max:500'],
            'full_description'  => ['nullable','string'],
            'technologies'      => ['nullable','string'],
            'result_summary'    => ['nullable','string','max:400'],
            'client_name'       => ['nullable','string','max:150'],
            'project_url'       => ['nullable','url','max:255'],
            'featured_image'    => ['nullable','image','max:3072'],
            'meta_title'        => ['nullable','string','max:200'],
            'meta_description'  => ['nullable','string','max:300'],
            'sort_order'        => ['nullable','integer'],
            'status'            => ['required','in:published,draft'],
        ]);

        if ($request->hasFile('featured_image')) {
            if ($project->featured_image) {
                Storage::disk('public')->delete($project->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')
                ->store('uploads/portfolio', 'public');
        }

        $technologies = null;
        if (!empty($validated['technologies'])) {
            $technologies = array_map('trim', explode(',', $validated['technologies']));
        }

        $project->update([
    'title'             => $validated['title'],
    'category'          => $validated['category'],
    'short_description' => $validated['short_description'],
    'full_description'  => $validated['full_description']  ?? null,
    'technologies'      => $technologies,
    'result_summary'    => $validated['result_summary']    ?? null,
    'client_name'       => $validated['client_name']       ?? null,
    'project_url'       => $validated['project_url']       ?? null,
    'featured_image'    => $validated['featured_image']    ?? $project->featured_image,
    'meta_title'        => $validated['meta_title']        ?? null,
    'meta_description'  => $validated['meta_description']  ?? null,
    'is_featured'       => $request->boolean('is_featured'),
    'sort_order'        => $validated['sort_order']        ?? $project->sort_order,
    'status'            => $validated['status'],
]);

        return redirect()->route('admin.portfolio')->with('success', 'Project updated.');
    }

    public function destroy(int $id)
    {
        $project = PortfolioProject::findOrFail($id);
        if ($project->featured_image) {
            Storage::disk('public')->delete($project->featured_image);
        }
        $project->delete();
        return redirect()->route('admin.portfolio')->with('success', 'Project deleted.');
    }
}