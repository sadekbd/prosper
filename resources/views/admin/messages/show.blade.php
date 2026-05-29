@extends('layouts.admin')
@section('title','View Message')
@section('page_title','Contact Message')
@section('page_subtitle','From: ' . $message->name)

@section('content')

<div class="flex items-center gap-3 mb-6">
  <a href="{{ route('admin.messages') }}" class="text-gray-400 hover:text-white transition-colors">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
  </a>
  <span class="text-gray-600">/</span>
  <span class="text-gray-400 text-sm">Message from {{ $message->name }}</span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  <div class="lg:col-span-2">
    <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-8">
      <div class="flex items-start justify-between mb-6">
        <div>
          <h2 class="text-xl font-bold text-white">{{ $message->name }}</h2>
          <p class="text-pm-cyan text-sm mt-1">{{ ucfirst(str_replace('_',' ',$message->service_interest)) }}</p>
        </div>
        <span class="px-3 py-1 rounded-xl text-xs font-bold
          {{ $message->status === 'new' ? 'bg-pm-cyan/15 text-pm-cyan' :
             ($message->status === 'replied' ? 'bg-green-500/15 text-green-400' : 'bg-gray-500/15 text-gray-400') }}">
          {{ ucfirst($message->status) }}
        </span>
      </div>

      <div class="prose prose-invert max-w-none">
        <p class="text-gray-300 leading-relaxed whitespace-pre-wrap">{{ $message->message }}</p>
      </div>

      <div class="mt-8 pt-6 border-t border-white/10 text-xs text-gray-500">
        Received {{ $message->created_at->format('F j, Y \a\t g:i A') }}
        @if($message->ip_address) · IP: {{ $message->ip_address }} @endif
      </div>
    </div>
  </div>

  <div class="space-y-5">
    {{-- Contact Info --}}
    <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6">
      <h3 class="text-white font-bold mb-4">Contact Details</h3>
      <div class="space-y-3">
        <div>
          <p class="text-gray-500 text-xs uppercase tracking-wide">Email</p>
          <a href="mailto:{{ $message->email }}" class="text-pm-cyan text-sm hover:underline">{{ $message->email }}</a>
        </div>
        @if($message->mobile)
          <div>
            <p class="text-gray-500 text-xs uppercase tracking-wide">Mobile</p>
            <a href="tel:{{ $message->mobile }}" class="text-white text-sm">{{ $message->mobile }}</a>
          </div>
        @endif
        @if($message->website_url)
          <div>
            <p class="text-gray-500 text-xs uppercase tracking-wide">Website</p>
            <a href="{{ $message->website_url }}" target="_blank" class="text-pm-cyan text-sm hover:underline truncate block">{{ $message->website_url }}</a>
          </div>
        @endif
      </div>

      {{-- Quick reply --}}
      <a href="mailto:{{ $message->email }}?subject=Re: Your enquiry to Prosper Media"
         class="btn-primary w-full justify-center text-sm mt-5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
        Reply via Email
      </a>
    </div>

    {{-- Update Status --}}
    <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-6">
      <h3 class="text-white font-bold mb-4">Update Status</h3>
      <div class="space-y-2">
        @foreach(['read'=>['Read','text-blue-400','bg-blue-500/10'], 'replied'=>['Replied','text-green-400','bg-green-500/10'], 'archived'=>['Archive','text-gray-400','bg-gray-500/10']] as $s=>[$lbl,$col,$bg])
          @if($message->status !== $s)
            <form action="{{ route('admin.messages.status', $message->id) }}" method="POST">
              @csrf @method('PATCH')
              <input type="hidden" name="status" value="{{ $s }}">
              <button type="submit"
                      class="w-full py-2.5 rounded-xl text-sm font-semibold border border-white/5 hover:border-white/15 transition-colors {{ $col }} {{ $bg }}">
                Mark as {{ $lbl }}
              </button>
            </form>
          @endif
        @endforeach
      </div>
    </div>

    {{-- Delete --}}
    <form action="{{ route('admin.messages.destroy', $message->id) }}" method="POST"
          onsubmit="return confirm('Permanently delete this message?')">
      @csrf @method('DELETE')
      <button type="submit" class="w-full py-3 bg-red-500/10 text-red-400 border border-red-500/20 rounded-xl text-sm font-semibold hover:bg-red-500/20 transition-colors">
        Delete Message
      </button>
    </form>
  </div>
</div>

@endsection