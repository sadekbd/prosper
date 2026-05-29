@extends('layouts.admin')
@section('title', 'Edit Article')
@section('page_title', 'Edit Article')
@section('page_subtitle', $article->title)

@push('head')
<style>
  .ql-toolbar { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1) !important; border-radius: 12px 12px 0 0; }
  .ql-container { border-color: rgba(255,255,255,0.1) !important; border-radius: 0 0 12px 12px; background: rgba(255,255,255,0.03); min-height: 350px; }
  .ql-editor { color: #e5e7eb; font-size: 15px; line-height: 1.7; }
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
  <span class="text-gray-400 text-sm">Edit Article</span>
</div>

<form action="{{ route('admin.blog.articles.update', $article->id) }}" method="POST"
      enctype="multipart/form-data" id="article-form">
  @csrf @method('PUT')

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    <div class="xl:col-span-2 space-y-5">

      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6">
        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Article Title *</label>
        <input type="text" name="title" value="{{ old('title', $article->title) }}"
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white
                      text-lg font-medium focus:outline-none focus:border-pm-cyan transition-colors">
        @error('title') <p class="text-red-400 text-xs mt-2">{{ $message }}</p> @enderror
      </div>

      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6">
        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Excerpt</label>
        <textarea name="excerpt" rows="2"
                  class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white
                         text-sm resize-none focus:outline-none focus:border-pm-cyan transition-colors">{{ old('excerpt', $article->excerpt) }}</textarea>
      </div>

      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6">
        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Article Content *</label>
        <div id="quill-editor"></div>
        <input type="hidden" name="content" id="content-input">
        @error('content') <p class="text-red-400 text-xs mt-2">{{ $message }}</p> @enderror
      </div>
    </div>

    <div class="space-y-5">

      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6">
        <h3 class="text-white font-bold text-sm mb-2">Status:
          <span class="text-pm-cyan ml-1">{{ ucfirst($article->status) }}</span>
        </h3>
        @if($user->isAdmin() && $article->status === 'review')
          <div class="flex flex-col gap-2 mb-3">
            <form action="{{ route('admin.blog.articles.publish', $article->id) }}" method="POST">
              @csrf @method('PATCH')
              <button type="submit" class="w-full py-2.5 bg-green-500/15 text-green-400 border border-green-500/20 rounded-xl text-sm font-semibold hover:bg-green-500/25 transition-colors">
                ✓ Publish This Article
              </button>
            </form>
            <form action="{{ route('admin.blog.articles.reject', $article->id) }}" method="POST">
              @csrf @method('PATCH')
              <button type="submit" class="w-full py-2.5 bg-red-500/10 text-red-400 border border-red-500/20 rounded-xl text-sm font-semibold hover:bg-red-500/20 transition-colors">
                ✗ Reject Article
              </button>
            </form>
          </div>
          <hr class="border-white/5 mb-3">
        @endif
        <div class="space-y-2">
          <button type="submit" name="action" value="draft"
                  class="w-full py-2.5 bg-white/5 text-gray-300 border border-white/10 rounded-xl text-sm font-semibold hover:bg-white/10 transition-colors">
            Save as Draft
          </button>
          <button type="submit" name="action" value="submit"
                  class="w-full btn-primary justify-center py-2.5 text-sm">
            {{ $user->isAdmin() ? 'Save & Publish' : 'Save & Submit for Review' }}
          </button>
        </div>
      </div>

      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6">
        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Category *</label>
        <select name="category_id"
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan">
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ $article->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6">
        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Featured Image</label>
        @if($article->featured_image)
          <img src="{{ Storage::url($article->featured_image) }}" alt="Current" class="w-full h-28 object-cover rounded-xl mb-3">
        @endif
        <input type="file" name="featured_image" accept="image/*"
               class="w-full text-sm text-gray-400 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-pm-cyan/10 file:text-pm-cyan hover:file:bg-pm-cyan/20 cursor-pointer">
      </div>

      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6">
        <h3 class="text-white font-bold text-sm mb-4">SEO</h3>
        <div class="space-y-3">
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Meta Title</label>
            <input type="text" name="meta_title" value="{{ old('meta_title', $article->meta_title) }}"
                   class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Meta Description</label>
            <textarea name="meta_description" rows="2"
                      class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-white text-sm resize-none focus:outline-none focus:border-pm-cyan transition-colors">{{ old('meta_description', $article->meta_description) }}</textarea>
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Tags</label>
            <input type="text" name="tags"
                   value="{{ old('tags', $article->tags ? implode(', ', $article->tags) : '') }}"
                   placeholder="tag1, tag2, tag3"
                   class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Read Time (min)</label>
            <input type="number" name="read_time" value="{{ old('read_time', $article->read_time) }}"
                   class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
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
    modules: { toolbar: [[{header:[2,3,false]}],['bold','italic','underline'],[{list:'ordered'},{list:'bullet'}],['link','blockquote','code-block'],['clean']] }
  });

  quill.root.innerHTML = {!! json_encode($article->content) !!};

  document.getElementById('article-form').addEventListener('submit', function() {
    document.getElementById('content-input').value = quill.root.innerHTML;
  });
</script>
@endpush

@endsection