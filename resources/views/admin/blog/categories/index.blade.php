@extends('layouts.admin')
@section('title', 'Blog Categories')
@section('page_title', 'Blog Categories')
@section('page_subtitle', 'Manage article categories')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

  {{-- Category List --}}
  <div class="lg:col-span-3">
    <div class="bg-[#1a2540] border border-white/5 rounded-2xl overflow-hidden">
      <div class="px-6 py-5 border-b border-white/5">
        <h3 class="text-white font-bold">All Categories</h3>
      </div>
      @if($categories->count() > 0)
        <div class="divide-y divide-white/5">
          @foreach($categories as $cat)
            <div class="flex items-center justify-between px-6 py-4 hover:bg-white/3 transition-colors">
              <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full" style="background-color: {{ $cat->color }}"></div>
                <div>
                  <p class="text-white font-semibold text-sm">{{ $cat->name }}</p>
                  <p class="text-gray-500 text-xs">{{ $cat->total_articles ?? 0 }} articles · {{ $cat->slug }}</p>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <a href="{{ route('admin.blog.categories.edit', $cat->id) }}"
                   class="p-1.5 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </a>
                <form action="{{ route('admin.blog.categories.destroy', $cat->id) }}" method="POST"
                      onsubmit="return confirm('Delete this category?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="p-1.5 text-gray-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </form>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="py-12 text-center text-gray-500">No categories yet.</div>
      @endif
    </div>
  </div>

  {{-- Add / Edit Form --}}
  <div class="lg:col-span-2">
    @php $editing = isset($category); @endphp
    <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6">
      <h3 class="text-white font-bold mb-5">{{ $editing ? 'Edit Category' : 'Add New Category' }}</h3>

      <form action="{{ $editing ? route('admin.blog.categories.update', $category->id) : route('admin.blog.categories.store') }}"
            method="POST" class="space-y-4">
        @csrf
        @if($editing) @method('PUT') @endif

        <div>
          <label class="block text-xs text-gray-400 mb-2">Name *</label>
          <input type="text" name="name" value="{{ old('name', $editing ? $category->name : '') }}"
                 placeholder="e.g. Google Ads Tips"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
          @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-xs text-gray-400 mb-2">Description</label>
          <textarea name="description" rows="2"
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm resize-none focus:outline-none focus:border-pm-cyan transition-colors"
                    placeholder="Brief category description...">{{ old('description', $editing ? $category->description : '') }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs text-gray-400 mb-2">Badge Color *</label>
            <div class="flex items-center gap-3">
              <input type="color" name="color" value="{{ old('color', $editing ? $category->color : '#00B4D8') }}"
                     class="w-12 h-10 rounded-lg border-0 bg-transparent cursor-pointer">
              <input type="text" id="color-text" value="{{ old('color', $editing ? $category->color : '#00B4D8') }}"
                     class="flex-1 bg-white/5 border border-white/10 rounded-xl px-3 py-2.5 text-white text-sm focus:outline-none focus:border-pm-cyan"
                     maxlength="7" placeholder="#00B4D8">
            </div>
          </div>
          <div>
            <label class="block text-xs text-gray-400 mb-2">Sort Order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $editing ? $category->sort_order : 0) }}"
                   min="0"
                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-pm-cyan">
          </div>
        </div>

        <div class="flex gap-3 pt-2">
          <button type="submit" class="flex-1 btn-primary justify-center text-sm py-3">
            {{ $editing ? 'Update Category' : 'Add Category' }}
          </button>
          @if($editing)
            <a href="{{ route('admin.blog.categories') }}"
               class="px-4 py-3 bg-white/5 text-gray-400 rounded-xl text-sm border border-white/10 hover:bg-white/10 transition-colors">
              Cancel
            </a>
          @endif
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // Sync color picker with text input
  const colorPicker = document.querySelector('input[type="color"]');
  const colorText   = document.getElementById('color-text');
  if (colorPicker && colorText) {
    colorPicker.addEventListener('input', () => colorText.value = colorPicker.value);
    colorText.addEventListener('input', () => colorPicker.value = colorText.value);
  }
</script>
@endpush

@endsection