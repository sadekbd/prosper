@extends('layouts.admin')
@section('title','New Project')
@section('page_title','New Project')
@section('page_subtitle','Add a portfolio case study')

@section('content')

<div class="flex items-center gap-3 mb-6">
  <a href="{{ route('admin.portfolio') }}" class="text-gray-400 hover:text-white transition-colors">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
  </a>
  <span class="text-gray-600">/</span>
  <span class="text-gray-400 text-sm">New Project</span>
</div>

<form action="{{ route('admin.portfolio.store') }}" method="POST" enctype="multipart/form-data">
  @csrf
  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    <div class="xl:col-span-2 space-y-5">
      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6 space-y-4">
        <div>
          <label class="block text-xs text-gray-400 mb-2">Project Title *</label>
          <input type="text" name="title" value="{{ old('title') }}"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
          @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs text-gray-400 mb-2">Category *</label>
            <select name="category" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan">
              @foreach(['google_ads'=>'Google Ads','tracking_setup'=>'Tracking Setup','web_development'=>'Web Development','landing_page'=>'Landing Page','automation'=>'Automation'] as $val=>$lbl)
                <option value="{{ $val }}" {{ old('category') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs text-gray-400 mb-2">Client Name</label>
            <input type="text" name="client_name" value="{{ old('client_name') }}"
                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
          </div>
        </div>

        <div>
          <label class="block text-xs text-gray-400 mb-2">Short Description * (shown in cards)</label>
          <textarea name="short_description" rows="3"
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm resize-none focus:outline-none focus:border-pm-cyan transition-colors">{{ old('short_description') }}</textarea>
          @error('short_description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-xs text-gray-400 mb-2">Full Description (case study page)</label>
          <textarea name="full_description" rows="6" placeholder="Detailed HTML content for the case study page..."
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm resize-none focus:outline-none focus:border-pm-cyan transition-colors font-mono text-xs">{{ old('full_description') }}</textarea>
        </div>

        <div>
          <label class="block text-xs text-gray-400 mb-2">Result Summary</label>
          <input type="text" name="result_summary" value="{{ old('result_summary') }}" placeholder="e.g. 320% ROAS in 90 days"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
        </div>

        <div>
          <label class="block text-xs text-gray-400 mb-2">Technologies (comma-separated)</label>
          <input type="text" name="technologies" value="{{ old('technologies') }}" placeholder="Google Ads, GTM, GA4, Laravel"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
        </div>

        <div>
          <label class="block text-xs text-gray-400 mb-2">Project URL</label>
          <input type="url" name="project_url" value="{{ old('project_url') }}" placeholder="https://..."
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
        </div>
      </div>
    </div>

    <div class="space-y-5">
      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6 space-y-4">
        <div>
          <label class="block text-xs text-gray-400 mb-2">Status</label>
          <select name="status" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan">
            <option value="draft"     {{ old('status','draft') === 'draft'     ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ old('status') === 'published'         ? 'selected' : '' }}>Published</option>
          </select>
        </div>

        <div>
          <label class="block text-xs text-gray-400 mb-2">Featured Image</label>
          <input type="file" name="featured_image" accept="image/*"
                 class="w-full text-sm text-gray-400 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-pm-cyan/10 file:text-pm-cyan hover:file:bg-pm-cyan/20 cursor-pointer">
          <p class="text-gray-500 text-xs mt-1">Max 3MB</p>
        </div>

        <div class="flex items-center gap-3">
          <input type="checkbox" name="is_featured" value="1" id="is_featured"
                 {{ old('is_featured') ? 'checked' : '' }}
                 class="w-4 h-4 rounded border-white/20 bg-white/5 text-pm-cyan focus:ring-pm-cyan/40">
          <label for="is_featured" class="text-sm text-gray-300 cursor-pointer">Featured project (shown first)</label>
        </div>

        <div>
          <label class="block text-xs text-gray-400 mb-2">Sort Order</label>
          <input type="number" name="sort_order" value="{{ old('sort_order',0) }}" min="0"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
        </div>

        <div>
          <label class="block text-xs text-gray-400 mb-2">Meta Title</label>
          <input type="text" name="meta_title" value="{{ old('meta_title') }}"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
        </div>
        <div>
          <label class="block text-xs text-gray-400 mb-2">Meta Description</label>
          <textarea name="meta_description" rows="2"
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm resize-none focus:outline-none focus:border-pm-cyan transition-colors">{{ old('meta_description') }}</textarea>
        </div>

        <button type="submit" class="w-full btn-primary justify-center py-3 text-sm mt-2">
          Create Project
        </button>
      </div>
    </div>

  </div>
</form>

@endsection