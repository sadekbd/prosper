@extends('layouts.admin')
@section('title','Services')
@section('page_title','Services')
@section('page_subtitle','Manage website service listings')

@section('content')

<div class="flex justify-end mb-6">
  <a href="{{ route('admin.services.create') }}" class="btn-primary text-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    New Service
  </a>
</div>

<div class="space-y-4">
  @forelse($services as $service)
    <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6 hover:border-white/10 transition-colors">
      <div class="flex items-start justify-between gap-4">
        <div class="flex-1">
          <div class="flex items-center gap-3 mb-2">
            <h3 class="text-white font-bold">{{ $service->title }}</h3>
            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold
              {{ $service->status === 'active' ? 'bg-green-500/10 text-green-400' : 'bg-gray-500/10 text-gray-400' }}">
              {{ ucfirst($service->status) }}
            </span>
          </div>
          <p class="text-pm-cyan text-sm mb-2">{{ $service->subtitle }}</p>
          <p class="text-gray-400 text-sm line-clamp-2">{{ $service->description }}</p>
          <p class="text-gray-600 text-xs mt-2">{{ $service->features_count }} features · Order: {{ $service->sort_order }}</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
          <a href="{{ route('services.show', $service->slug) }}" target="_blank"
             class="p-2 text-gray-500 hover:text-pm-cyan rounded-lg hover:bg-white/5 transition-colors" title="View public">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
          </a>
          <a href="{{ route('admin.services.edit', $service->id) }}"
             class="p-2 text-gray-400 hover:text-white rounded-lg hover:bg-white/10 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
          </a>
          <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Delete this service?')">
            @csrf @method('DELETE')
            <button type="submit" class="p-2 text-gray-500 hover:text-red-400 rounded-lg hover:bg-red-500/10 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </form>
        </div>
      </div>
    </div>
  @empty
    <div class="bg-[#1a2540] border border-white/5 rounded-2xl py-16 text-center">
      <p class="text-gray-400">No services yet.</p>
      <a href="{{ route('admin.services.create') }}" class="text-pm-cyan text-sm hover:underline mt-2 block">Create your first service →</a>
    </div>
  @endforelse
</div>

@endsection