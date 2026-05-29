@extends('layouts.admin')
@section('title', 'New Article')
@section('page_title', 'New Article')
@section('page_subtitle', 'Create and publish blog content')

@push('head')
<style>
  .ql-toolbar { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1) !important; border-radius: 12px 12px 0 0; }
  .ql-container { border-color: rgba(255,255,255,0.1) !important; border-radius: 0 0 12px 12px; background: rgba(255,255,255,0.03); min-height: 350px; }
  .ql-editor { color: #e5e7eb; font-size: 15px; line-height: 1.7; }
  .ql-editor p { margin-bottom: 1em; }
  .ql-stroke { stroke: #9ca3af !important; }
  .ql-fill { fill: #9ca3af !important; }
  .ql-picker { color: #9ca3af !important; }
</style>
@endpush

@section('content')
@php $user = auth('admin')->user(); @endphp

<div class="flex items-center gap-3 mb-6">
  <a href="{{ route('admin.blog.articles') }}" class="text-gray-400 hover:text-white transition-colors">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
  </a>
  <span class="text-gray-600">/</span>
  <span class="text-gray-400 text-sm">New Article</span>
</div>

<form action="{{ route('admin.blog.articles.store') }}" method="POST" enctype="multipart/form-data" id="article-form">
  @csrf

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Main Content --}}
    <div class="xl:col-span-2 space-y-5">

      {{-- Title --}}
      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6">
        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">
          Article Title *
        </label>
        <input type="text" name="title" value="{{ old('title') }}"
               placeholder="Write a clear, compelling article title..."
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white
                      placeholder-gray-500 text-lg font-medium focus:outline-none focus:border-pm-cyan transition-colors">
        @error('title') <p class="text-red-400 text-xs mt-2">{{ $message }}</p> @enderror
      </div>

      {{-- Excerpt --}}
      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6">
        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">
          Excerpt / Summary
        </label>
        <textarea name="excerpt" rows="2" placeholder="Brief summary shown in article listings..."
                  class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white
                         placeholder-gray-500 text-sm resize-none focus:outline-none focus:border-pm-cyan transition-colors">{{ old('excerpt') }}</textarea>
        @error('excerpt') <p class="text-red-400 text-xs mt-2">{{ $message }}</p> @enderror
      </div>

      {{-- Content editor --}}
      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6">
        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">
          Article Content *
        </label>
        <div id="quill-editor">{!! old('content') !!}</div>
        <input type="hidden" name="content" id="content-input">
        @error('content') <p class="text-red-400 text-xs mt-2">{{ $message }}</p> @enderror
      </div>

    </div>

    {{-- Sidebar --}}
    <div class="space-y-5">

      {{-- Publish box --}}
      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6">
        <h3 class="text-white font-bold text-sm mb-4">Publish</h3>
        <div class="space-y-3">
          <button type="submit" name="action" value="draft"
                  class="w-full flex items-center justify-center gap-2 py-3 bg-white/5 text-gray-300
                         border border-white/10 rounded-xl text-sm font-semibold
                         hover:bg-white/10 transition-colors">
            Save as Draft
          </button>
          <button type="submit" name="action" value="submit"
                  class="w-full btn-primary justify-center py-3 text-sm">
            {{ $user->isAdmin() ? 'Publish Now' : 'Submit for Review' }}
          </button>
        </div>
        @if(!$user->isAdmin())
          <p class="text-gray-500 text-xs text-center mt-3">
            Your article will be reviewed by an admin before publishing.
          </p>
        @endif
      </div>

      {{-- Category --}}
      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6">
        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Category *</label>
        <select name="category_id"
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white
                       text-sm focus:outline-none focus:border-pm-cyan transition-colors">
          <option value="">— Select category —</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
              {{ $cat->name }}
            </option>
          @endforeach
        </select>
        @error('category_id') <p class="text-red-400 text-xs mt-2">{{ $message }}</p> @enderror
      </div>

      {{-- Featured Image --}}
      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6">
        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Featured Image</label>
        <input type="file" name="featured_image" accept="image/*"
               class="w-full text-sm text-gray-400
                      file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                      file:text-xs file:font-semibold file:bg-pm-cyan/10 file:text-pm-cyan
                      hover:file:bg-pm-cyan/20 cursor-pointer">
        <p class="text-gray-500 text-xs mt-2">JPG, PNG, WebP — max 2MB</p>
        @error('featured_image') <p class="text-red-400 text-xs mt-2">{{ $message }}</p> @enderror
      </div>

      {{-- SEO --}}
      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6">
        <h3 class="text-white font-bold text-sm mb-4">SEO</h3>
        <div class="space-y-3">
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Meta Title</label>
            <input type="text" name="meta_title" value="{{ old('meta_title') }}" placeholder="SEO title..."
                   class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2.5 text-white
                          text-sm focus:outline-none focus:border-pm-cyan transition-colors placeholder-gray-600">
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Meta Description</label>
            <textarea name="meta_description" rows="2" placeholder="SEO description..."
                      class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2.5 text-white
                             text-sm resize-none focus:outline-none focus:border-pm-cyan transition-colors placeholder-gray-600">{{ old('meta_description') }}</textarea>
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Tags (comma-separated)</label>
            <input type="text" name="tags" value="{{ old('tags') }}" placeholder="google-ads, tracking, gtm"
                   class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2.5 text-white
                          text-sm focus:outline-none focus:border-pm-cyan transition-colors placeholder-gray-600">
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Read Time (minutes)</label>
            <input type="number" name="read_time" value="{{ old('read_time') }}" min="1" max="120" placeholder="10"
                   class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2.5 text-white
                          text-sm focus:outline-none focus:border-pm-cyan transition-colors placeholder-gray-600">
          </div>
        </div>
      </div>

    </div>
  </div>
</form>

@push('scripts')
<link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.snow.css">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
  var quill = new Quill('#quill-editor', {
    theme: 'snow',
    modules: {
      toolbar: [
        [{ header: [2, 3, false] }],
        ['bold', 'italic', 'underline'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        ['link', 'blockquote', 'code-block'],
        ['clean']
      ]
    }
  });

  // Set initial content from old() if any
  @if(old('content'))
    quill.root.innerHTML = {!! json_encode(old('content')) !!};
  @endif

  // Copy quill content to hidden input on submit
  document.getElementById('article-form').addEventListener('submit', function() {
    document.getElementById('content-input').value = quill.root.innerHTML;
  });
</script>
@endpush

@endsection