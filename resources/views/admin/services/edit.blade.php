@extends('layouts.admin')
@section('title','Edit Service')
@section('page_title','Edit Service')
@section('page_subtitle', $service->title)

@section('content')

<div class="flex items-center gap-3 mb-6">
  <a href="{{ route('admin.services') }}" class="text-gray-400 hover:text-white transition-colors">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
  </a>
  <span class="text-gray-600">/</span>
  <span class="text-gray-400 text-sm">Edit: {{ $service->title }}</span>
</div>

<form action="{{ route('admin.services.update', $service->id) }}" method="POST" x-data="featuresManager()">
  @csrf @method('PUT')

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 space-y-5">

      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6 space-y-4">
        <h3 class="text-white font-bold mb-2">Service Details</h3>
        <div>
          <label class="block text-xs text-gray-400 mb-2">Title *</label>
          <input type="text" name="title" value="{{ old('title', $service->title) }}"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
        </div>
        <div>
          <label class="block text-xs text-gray-400 mb-2">Subtitle</label>
          <input type="text" name="subtitle" value="{{ old('subtitle', $service->subtitle) }}"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
        </div>
        <div>
          <label class="block text-xs text-gray-400 mb-2">Description *</label>
          <textarea name="description" rows="5"
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm resize-none focus:outline-none focus:border-pm-cyan transition-colors">{{ old('description', $service->description) }}</textarea>
        </div>
      </div>

      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-white font-bold">Service Features</h3>
          <button type="button" @click="addFeature()" class="text-pm-cyan text-sm font-semibold hover:underline flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add
          </button>
        </div>
        <div class="space-y-2">
          <template x-for="(feature, index) in features" :key="index">
            <div class="flex items-center gap-2">
              <input type="text" :name="`features[${index}]`" x-model="features[index]"
                     class="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
              <button type="button" @click="removeFeature(index)" class="p-2 text-gray-500 hover:text-red-400 rounded-lg hover:bg-red-500/10 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>
          </template>
        </div>
      </div>
    </div>

    <div class="space-y-5">
      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6 space-y-4">
        <h3 class="text-white font-bold mb-2">Settings</h3>
        <div>
          <label class="block text-xs text-gray-400 mb-2">Status</label>
          <select name="status" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan">
            <option value="active"   {{ $service->status === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ $service->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
          </select>
        </div>
        <div>
          <label class="block text-xs text-gray-400 mb-2">Sort Order</label>
          <input type="number" name="sort_order" value="{{ old('sort_order', $service->sort_order) }}" min="0"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
        </div>
        <div>
          <label class="block text-xs text-gray-400 mb-2">Meta Title</label>
          <input type="text" name="meta_title" value="{{ old('meta_title', $service->meta_title) }}"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
        </div>
        <div>
          <label class="block text-xs text-gray-400 mb-2">Meta Description</label>
          <textarea name="meta_description" rows="2"
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm resize-none focus:outline-none focus:border-pm-cyan transition-colors">{{ old('meta_description', $service->meta_description) }}</textarea>
        </div>
        <button type="submit" class="w-full btn-primary justify-center py-3 text-sm mt-2">
          Update Service
        </button>
      </div>
    </div>
  </div>
</form>

@push('scripts')
<script>
function featuresManager() {
  return {
    features: @json(old('features', $service->features->pluck('feature')->toArray())),
    addFeature()    { this.features.push(''); },
    removeFeature(i){ this.features.splice(i, 1); }
  }
}
</script>
@endpush

@endsection