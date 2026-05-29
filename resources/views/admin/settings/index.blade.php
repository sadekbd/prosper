@extends('layouts.admin')
@section('title','Site Settings')
@section('page_title','Site Settings')
@section('page_subtitle','Manage global website configuration')

@section('content')

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
  @csrf

  <div class="space-y-6">
    @foreach($settings as $group => $groupSettings)
      <div class="bg-[#1a2540] border border-white/5 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/5">
          <h3 class="text-white font-bold capitalize">{{ $group }} Settings</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
          @foreach($groupSettings as $setting)
            <div class="{{ $setting->type === 'textarea' ? 'md:col-span-2' : '' }}">
              <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">
                {{ $setting->label }}
              </label>

              @if($setting->type === 'textarea')
                <textarea name="{{ $setting->key }}" rows="3"
                          class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm resize-none focus:outline-none focus:border-pm-cyan transition-colors">{{ $setting->value }}</textarea>

              @elseif($setting->type === 'image')
                <div>
                  @if($setting->value)
                    <img src="{{ Storage::url($setting->value) }}" alt="{{ $setting->label }}"
                         class="h-14 mb-2 rounded-lg object-contain bg-white/5 px-2">
                  @endif
                  <input type="file" name="{{ $setting->key }}" accept="image/*"
                         class="w-full text-sm text-gray-400 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-pm-cyan/10 file:text-pm-cyan hover:file:bg-pm-cyan/20 cursor-pointer">
                </div>

              @elseif($setting->type === 'boolean')
                <div class="flex items-center gap-3 mt-1">
                  <input type="hidden" name="{{ $setting->key }}" value="0">
                  <input type="checkbox" name="{{ $setting->key }}" value="1"
                         {{ $setting->value ? 'checked' : '' }}
                         class="w-5 h-5 rounded border-white/20 bg-white/5 text-pm-cyan focus:ring-pm-cyan/40">
                  <span class="text-gray-400 text-sm">Enabled</span>
                </div>

              @else
                <input type="{{ $setting->type }}" name="{{ $setting->key }}" value="{{ $setting->value }}"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-pm-cyan transition-colors">
              @endif
            </div>
          @endforeach
        </div>
      </div>
    @endforeach

    <div class="flex justify-end">
      <button type="submit" class="btn-primary px-8 py-3">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        Save All Settings
      </button>
    </div>
  </div>
</form>

@endsection