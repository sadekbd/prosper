<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceFeature;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::withCount('features')->orderBy('sort_order')->get();
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => ['required','string','max:150'],
            'subtitle'         => ['nullable','string','max:255'],
            'description'      => ['required','string'],
            'icon'             => ['nullable','string','max:100'],
            'meta_title'       => ['nullable','string','max:200'],
            'meta_description' => ['nullable','string','max:300'],
            'sort_order'       => ['nullable','integer','min:0'],
            'status'           => ['required','in:active,inactive'],
            'features'         => ['nullable','array'],
            'features.*'       => ['string','max:255'],
        ]);

        $slug  = Str::slug($validated['title']);
        $count = Service::where('slug','like',"{$slug}%")->count();

        DB::transaction(function () use ($validated, $slug, $count) {
            $service = Service::create([
                'title'            => $validated['title'],
                'slug'             => $count ? "{$slug}-{$count}" : $slug,
                'subtitle'         => $validated['subtitle'],
                'description'      => $validated['description'],
                'icon'             => $validated['icon'],
                'meta_title'       => $validated['meta_title'],
                'meta_description' => $validated['meta_description'],
                'sort_order'       => $validated['sort_order'] ?? 0,
                'status'           => $validated['status'],
            ]);

            // Save features
            if (!empty($validated['features'])) {
                foreach (array_filter($validated['features']) as $i => $feat) {
                    ServiceFeature::create([
                        'service_id' => $service->id,
                        'feature'    => $feat,
                        'sort_order' => $i + 1,
                    ]);
                }
            }

            ActivityLog::log('created_service', 'Service', $service->id, "Created: {$service->title}");
        });

        return redirect()->route('admin.services')->with('success', 'Service created successfully.');
    }

    public function edit(int $id)
    {
        $service = Service::with('features')->findOrFail($id);
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, int $id)
    {
        $service   = Service::findOrFail($id);
        $validated = $request->validate([
            'title'            => ['required','string','max:150'],
            'subtitle'         => ['nullable','string','max:255'],
            'description'      => ['required','string'],
            'icon'             => ['nullable','string','max:100'],
            'meta_title'       => ['nullable','string','max:200'],
            'meta_description' => ['nullable','string','max:300'],
            'sort_order'       => ['nullable','integer','min:0'],
            'status'           => ['required','in:active,inactive'],
            'features'         => ['nullable','array'],
            'features.*'       => ['string','max:255'],
        ]);

        DB::transaction(function () use ($service, $validated) {
            $service->update([
                'title'            => $validated['title'],
                'subtitle'         => $validated['subtitle'],
                'description'      => $validated['description'],
                'icon'             => $validated['icon'],
                'meta_title'       => $validated['meta_title'],
                'meta_description' => $validated['meta_description'],
                'sort_order'       => $validated['sort_order'] ?? 0,
                'status'           => $validated['status'],
            ]);

            // Replace all features
            $service->features()->delete();
            if (!empty($validated['features'])) {
                foreach (array_filter($validated['features']) as $i => $feat) {
                    ServiceFeature::create([
                        'service_id' => $service->id,
                        'feature'    => $feat,
                        'sort_order' => $i + 1,
                    ]);
                }
            }

            ActivityLog::log('updated_service', 'Service', $service->id);
        });

        return redirect()->route('admin.services')->with('success', 'Service updated.');
    }

    public function destroy(int $id)
    {
        $service = Service::findOrFail($id);
        $service->delete(); // cascades to features
        ActivityLog::log('deleted_service', 'Service', $id, "Deleted: {$service->title}");
        return redirect()->route('admin.services')->with('success', 'Service deleted.');
    }
}