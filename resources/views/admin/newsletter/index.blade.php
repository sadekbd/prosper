@extends('layouts.admin')
@section('title','Newsletter')
@section('page_title','Newsletter Subscribers')
@section('page_subtitle','Manage email subscriber list')

@section('content')

<div class="grid grid-cols-2 gap-4 mb-6">
  <div class="bg-[#1a2540] border border-white/5 rounded-xl p-5 text-center">
    <p class="text-3xl font-extrabold text-pm-cyan font-heading">{{ $counts['active'] }}</p>
    <p class="text-gray-400 text-sm mt-1">Active Subscribers</p>
  </div>
  <div class="bg-[#1a2540] border border-white/5 rounded-xl p-5 text-center">
    <p class="text-3xl font-extrabold text-gray-400 font-heading">{{ $counts['unsubscribed'] }}</p>
    <p class="text-gray-400 text-sm mt-1">Unsubscribed</p>
  </div>
</div>

<div class="flex gap-2 mb-6">
  @foreach(['active'=>'Active','unsubscribed'=>'Unsubscribed','all'=>'All'] as $s=>$l)
    <a href="{{ route('admin.newsletter', ['status'=>$s]) }}"
       class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all
              {{ $status === $s ? 'bg-pm-cyan text-white border-pm-cyan' : 'bg-white/5 text-gray-400 border-white/10 hover:border-white/20' }}">
      {{ $l }}
    </a>
  @endforeach
</div>

<div class="bg-[#1a2540] border border-white/5 rounded-2xl overflow-hidden">
  @if($subscribers->count() > 0)
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b border-white/5">
          <tr>
            <th class="text-left px-5 py-3.5 text-gray-500 text-xs uppercase tracking-wide font-semibold">Email</th>
            <th class="text-left px-5 py-3.5 text-gray-500 text-xs uppercase tracking-wide font-semibold hidden md:table-cell">Name</th>
            <th class="text-left px-5 py-3.5 text-gray-500 text-xs uppercase tracking-wide font-semibold">Status</th>
            <th class="text-left px-5 py-3.5 text-gray-500 text-xs uppercase tracking-wide font-semibold hidden lg:table-cell">Subscribed</th>
            <th class="text-right px-5 py-3.5 text-gray-500 text-xs uppercase tracking-wide font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
          @foreach($subscribers as $sub)
            <tr class="hover:bg-white/3 transition-colors">
              <td class="px-5 py-3"><p class="text-white text-sm">{{ $sub->email }}</p></td>
              <td class="px-5 py-3 hidden md:table-cell"><p class="text-gray-400 text-sm">{{ $sub->name ?? '—' }}</p></td>
              <td class="px-5 py-3">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold
                  {{ $sub->status === 'active' ? 'bg-green-500/10 text-green-400' : 'bg-gray-500/10 text-gray-400' }}">
                  {{ ucfirst($sub->status) }}
                </span>
              </td>
              <td class="px-5 py-3 hidden lg:table-cell">
                <p class="text-gray-500 text-xs">{{ $sub->subscribed_at->format('M j, Y') }}</p>
              </td>
              <td class="px-5 py-3 text-right">
                <form action="{{ route('admin.newsletter.destroy', $sub->id) }}" method="POST" onsubmit="return confirm('Remove this subscriber?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="p-1.5 text-gray-500 hover:text-red-400 rounded-lg hover:bg-red-500/10 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @if($subscribers->hasPages())
      <div class="px-5 py-4 border-t border-white/5">{{ $subscribers->links() }}</div>
    @endif
  @else
    <div class="py-16 text-center text-gray-500">No subscribers yet.</div>
  @endif
</div>

@endsection