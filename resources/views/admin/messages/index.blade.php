@extends('layouts.admin')
@section('title','Contact Messages')
@section('page_title','Contact Messages')
@section('page_subtitle','All incoming lead messages')

@section('content')

{{-- Status tabs --}}
<div class="flex flex-wrap gap-2 mb-6">
  @foreach(['all'=>'All','new'=>'New','read'=>'Read','replied'=>'Replied','archived'=>'Archived'] as $s=>$l)
    <a href="{{ route('admin.messages', array_merge(request()->query(), ['status'=>$s])) }}"
       class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all flex items-center gap-1.5
              {{ $status === $s ? 'bg-pm-cyan text-white border-pm-cyan' : 'bg-white/5 text-gray-400 border-white/10 hover:border-white/20' }}">
      {{ $l }}
      <span class="bg-white/10 px-1.5 py-0.5 rounded text-[9px] font-bold">{{ $counts[$s] }}</span>
    </a>
  @endforeach
</div>

{{-- Search --}}
<form method="GET" class="mb-6 flex gap-3">
  <input type="hidden" name="status" value="{{ $status }}">
  <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or email..."
         class="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-pm-cyan transition-colors">
  <button type="submit" class="btn-primary text-sm py-2.5 px-4">Search</button>
  @if($search)
    <a href="{{ route('admin.messages', ['status'=>$status]) }}" class="px-4 py-2.5 bg-white/5 text-gray-400 rounded-xl text-sm border border-white/10">Clear</a>
  @endif
</form>

<div class="bg-[#1a2540] border border-white/5 rounded-2xl overflow-hidden">
  @if($messages->count() > 0)
    <div class="divide-y divide-white/5">
      @foreach($messages as $msg)
        <div class="px-6 py-4 hover:bg-white/3 transition-colors">
          <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-4 min-w-0">
              {{-- Unread dot --}}
              <div class="mt-1.5 flex-shrink-0">
                @if($msg->status === 'new')
                  <div class="w-2 h-2 bg-pm-cyan rounded-full"></div>
                @else
                  <div class="w-2 h-2 bg-transparent rounded-full"></div>
                @endif
              </div>
              <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                  <p class="text-white font-semibold text-sm">{{ $msg->name }}</p>
                  <span class="px-2 py-0.5 rounded text-[9px] font-bold
                    {{ $msg->status === 'new' ? 'bg-pm-cyan/15 text-pm-cyan' :
                       ($msg->status === 'replied' ? 'bg-green-500/15 text-green-400' :
                       ($msg->status === 'archived' ? 'bg-gray-500/15 text-gray-400' : 'bg-blue-500/15 text-blue-400')) }}">
                    {{ ucfirst($msg->status) }}
                  </span>
                </div>
                <p class="text-gray-400 text-xs">{{ $msg->email }} · {{ $msg->mobile }}</p>
                <p class="text-pm-cyan text-xs mt-0.5">{{ ucfirst(str_replace('_',' ',$msg->service_interest)) }}</p>
                <p class="text-gray-500 text-xs mt-1 line-clamp-1">{{ $msg->message }}</p>
              </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
              <span class="text-gray-600 text-xs hidden md:block">{{ $msg->created_at->diffForHumans() }}</span>
              <a href="{{ route('admin.messages.show', $msg->id) }}"
                 class="p-1.5 text-gray-400 hover:text-pm-cyan rounded-lg hover:bg-white/10 transition-colors" title="View">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              </a>
              <form action="{{ route('admin.messages.destroy', $msg->id) }}" method="POST" onsubmit="return confirm('Delete this message?')">
                @csrf @method('DELETE')
                <button type="submit" class="p-1.5 text-gray-500 hover:text-red-400 rounded-lg hover:bg-red-500/10 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              </form>
            </div>
          </div>
        </div>
      @endforeach
    </div>
    @if($messages->hasPages())
      <div class="px-6 py-4 border-t border-white/5">{{ $messages->links() }}</div>
    @endif
  @else
    <div class="py-16 text-center text-gray-500">No messages found.</div>
  @endif
</div>

@endsection