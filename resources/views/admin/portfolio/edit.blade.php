@extends('layouts.admin')
@section('title','Edit Project')
@section('page_title','Edit Project')
@section('page_subtitle', $project->title)

@section('content')

<div class="flex items-center gap-3 mb-6">
  <a href="{{ route('admin.portfolio') }}" class="text-gray-400 hover:text-white transition-colors">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
  </a>
  <span class="text-gray-600">/</span>
  <span class="text-gray-400 text-sm truncate">{{ $project->title }}</span>
</div>

<form action="{{ route('admin.portfolio.update', $project->id) }}" method="POST" enctype="multipart/form-data">
  @csrf @method('PUT')

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 space-y-5">
      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6 space-y-4">
        <div>
          <label class="block text-xs text-gray-400 mb-2">Title *</label>
          <input type="text" name="title" value="{{ old('title',$project->title) }}"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs text-gray-400 mb-2">Category *</label>
            <select name="category" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan">
              @foreach(['google_ads'=>'Google Ads','tracking_setup'=>'Tracking Setup','web_development'=>'Web Development','landing_page'=>'Landing Page','automation'=>'Automation'] as $val=>$lbl)
                <option value="{{ $val }}" {{ $project->category === $val ? 'selected' : '' }}>{{ $lbl }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs text-gray-400 mb-2">Client Name</label>
            <input type="text" name="client_name" value="{{ old('client_name',$project->client_name) }}"
                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
          </div>
        </div>
        <div>
          <label class="block text-xs text-gray-400 mb-2">Short Description *</label>
          <textarea name="short_description" rows="3"
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm resize-none focus:outline-none focus:border-pm-cyan transition-colors">{{ old('short_description',$project->short_description) }}</textarea>
        </div>
        <div>
          <label class="block text-xs text-gray-400 mb-2">Full Description (HTML)</label>
          <textarea name="full_description" rows="8"
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm resize-none focus:outline-none focus:border-pm-cyan transition-colors font-mono text-xs">{{ old('full_description',$project->full_description) }}</textarea>
        </div>
        <div>
          <label class="block text-xs text-gray-400 mb-2">Result Summary</label>
          <input type="text" name="result_summary" value="{{ old('result_summary',$project->result_summary) }}"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
        </div>
        <div>
          <label class="block text-xs text-gray-400 mb-2">Technologies</label>
          <input type="text" name="technologies" value="{{ old('technologies', $project->technologies ? implode(', ',$project->technologies) : '') }}"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
        </div>
        <div>
          <label class="block text-xs text-gray-400 mb-2">Project URL</label>
          <input type="url" name="project_url" value="{{ old('project_url',$project->project_url) }}"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
        </div>
      </div>
    </div>

    <div class="space-y-5">
      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6 space-y-4">
        <div>
          <label class="block text-xs text-gray-400 mb-2">Status</label>
          <select name="status" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan">
            <option value="draft"     {{ $project->status === 'draft'     ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ $project->status === 'published' ? 'selected' : '' }}>Published</option>
          </select>
        </div>
        <div>
          <label class="block text-xs text-gray-400 mb-2">Featured Image</label>
          @if($project->featured_image)
            <img src="{{ Storage::url($project->featured_image) }}" class="w-full h-32 object-cover rounded-xl mb-2">
          @endif
          <input type="file" name="featured_image" accept="image/*"
                 class="w-full text-sm text-gray-400 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-pm-cyan/10 file:text-pm-cyan hover:file:bg-pm-cyan/20 cursor-pointer">
        </div>
        <div class="flex items-center gap-3">
          <input type="checkbox" name="is_featured" value="1" id="is_featured"
                 {{ $project->is_featured ? 'checked' : '' }}
                 class="w-4 h-4 rounded border-white/20 bg-white/5 text-pm-cyan focus:ring-pm-cyan/40">
          <label for="is_featured" class="text-sm text-gray-300 cursor-pointer">Featured project</label>
        </div>
        <div>
          <label class="block text-xs text-gray-400 mb-2">Sort Order</label>
          <input type="number" name="sort_order" value="{{ old('sort_order',$project->sort_order) }}" min="0"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
        </div>
        <button type="submit" class="w-full btn-primary justify-center py-3 text-sm mt-2">
          Update Project
        </button>
      </div>
    </div>
  </div>
</form>

@endsection