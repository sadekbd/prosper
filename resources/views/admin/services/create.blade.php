@extends('layouts.admin')
@section('title','New Service')
@section('page_title','New Service')
@section('page_subtitle','Add a new service to the website')

@section('content')

<div class="flex items-center gap-3 mb-6">
  <a href="{{ route('admin.services') }}" class="text-gray-400 hover:text-white transition-colors">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
  </a>
  <span class="text-gray-600">/</span>
  <span class="text-gray-400 text-sm">New Service</span>
</div>

<form action="{{ route('admin.services.store') }}" method="POST" x-data="featuresManager()">
  @csrf

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    <div class="xl:col-span-2 space-y-5">

      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6 space-y-4">
        <h3 class="text-white font-bold mb-2">Service Details</h3>

        <div>
          <label class="block text-xs text-gray-400 mb-2">Title *</label>
          <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Google Ads Mastery"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
          @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-xs text-gray-400 mb-2">Subtitle</label>
          <input type="text" name="subtitle" value="{{ old('subtitle') }}" placeholder="Short compelling subtitle..."
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
        </div>

        <div>
          <label class="block text-xs text-gray-400 mb-2">Description *</label>
          <textarea name="description" rows="5" placeholder="Detailed service description..."
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm resize-none focus:outline-none focus:border-pm-cyan transition-colors">{{ old('description') }}</textarea>
          @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
      </div>

      {{-- Features --}}
      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-white font-bold">Service Features</h3>
          <button type="button" @click="addFeature()"
                  class="text-pm-cyan text-sm font-semibold hover:underline flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Feature
          </button>
        </div>
        <div class="space-y-2">
          <template x-for="(feature, index) in features" :key="index">
            <div class="flex items-center gap-2">
              <input type="text" :name="`features[${index}]`" x-model="features[index]"
                     placeholder="e.g. Search & Display Campaigns"
                     class="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
              <button type="button" @click="removeFeature(index)"
                      class="p-2 text-gray-500 hover:text-red-400 rounded-lg hover:bg-red-500/10 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>
          </template>
          <p x-show="features.length === 0" class="text-gray-500 text-sm">No features yet. Click "Add Feature" to start.</p>
        </div>
      </div>

    </div>

    <div class="space-y-5">
      <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6 space-y-4">
        <h3 class="text-white font-bold mb-2">Settings</h3>

        <div>
          <label class="block text-xs text-gray-400 mb-2">Status</label>
          <select name="status" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan">
            <option value="active" {{ old('status','active') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
          </select>
        </div>

        <div>
          <label class="block text-xs text-gray-400 mb-2">Icon Name</label>
          <input type="text" name="icon" value="{{ old('icon') }}" placeholder="e.g. google-ads"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
        </div>

        <div>
          <label class="block text-xs text-gray-400 mb-2">Sort Order</label>
          <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
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
          Create Service
        </button>
      </div>
    </div>

  </div>
</form>

@push('scripts')
<script>
function featuresManager() {
  return {
    features: @json(old('features', [])),
    addFeature()    { this.features.push(''); },
    removeFeature(i){ this.features.splice(i, 1); }
  }
}
</script>
@endpush

@endsection