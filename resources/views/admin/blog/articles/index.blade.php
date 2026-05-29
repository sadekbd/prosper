@extends('layouts.admin')
@section('title', 'Blog Articles')
@section('page_title', 'Blog Articles')
@section('page_subtitle', 'Manage all blog content')

@section('content')
@php $user = auth('admin')->user(); @endphp

<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
  <div class="flex flex-wrap gap-2">
    @foreach(['all'=>'All','draft'=>'Draft','review'=>'Review','published'=>'Published','rejected'=>'Rejected'] as $s=>$l)
      <a href="{{ route('admin.blog.articles', array_merge(request()->query(), ['status'=>$s])) }}"
         class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all
                {{ $status === $s ? 'bg-pm-cyan text-white border-pm-cyan' : 'bg-white/5 text-gray-400 border-white/10 hover:border-white/20' }}">
        {{ $l }}
      </a>
    @endforeach
  </div>
  <a href="{{ route('admin.blog.articles.create') }}" class="btn-primary text-sm py-2.5">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
    </svg>
    New Article
  </a>
</div>

{{-- Search --}}
<form method="GET" action="{{ route('admin.blog.articles') }}" class="mb-6">
  <input type="hidden" name="status" value="{{ $status }}">
  <div class="flex gap-3">
    <input type="text" name="search" value="{{ $search }}"
           placeholder="Search articles..."
           class="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white
                  placeholder-gray-500 text-sm focus:outline-none focus:border-pm-cyan transition-colors">
    <button type="submit" class="btn-primary text-sm py-2.5 px-4">Search</button>
    @if($search)
      <a href="{{ route('admin.blog.articles', ['status'=>$status]) }}"
         class="px-4 py-2.5 bg-white/5 text-gray-400 rounded-xl text-sm border border-white/10 hover:bg-white/10 transition-colors">Clear</a>
    @endif
  </div>
</form>

{{-- Table --}}
<div class="bg-[#1a2540] border border-white/5 rounded-2xl overflow-hidden">
  @if($articles->count() > 0)
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b border-white/5">
          <tr class="text-left">
            <th class="px-5 py-3.5 text-gray-500 font-semibold text-xs uppercase tracking-wide">Title</th>
            <th class="px-5 py-3.5 text-gray-500 font-semibold text-xs uppercase tracking-wide hidden md:table-cell">Category</th>
            <th class="px-5 py-3.5 text-gray-500 font-semibold text-xs uppercase tracking-wide hidden lg:table-cell">Author</th>
            <th class="px-5 py-3.5 text-gray-500 font-semibold text-xs uppercase tracking-wide">Status</th>
            <th class="px-5 py-3.5 text-gray-500 font-semibold text-xs uppercase tracking-wide hidden lg:table-cell">Date</th>
            <th class="px-5 py-3.5 text-gray-500 font-semibold text-xs uppercase tracking-wide text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
          @foreach($articles as $article)
            <tr class="hover:bg-white/3 transition-colors">
              <td class="px-5 py-4">
                <p class="text-white font-medium leading-snug max-w-xs truncate">{{ $article->title }}</p>
                <p class="text-gray-500 text-xs mt-0.5">{{ number_format($article->views) }} views</p>
              </td>
              <td class="px-5 py-4 hidden md:table-cell">
                @if($article->category)
                  <span class="px-2 py-1 rounded-lg text-[10px] font-semibold text-white"
                        style="background-color: {{ $article->category->color ?? '#00B4D8' }}90">
                    {{ $article->category->name }}
                  </span>
                @else <span class="text-gray-600">—</span> @endif
              </td>
              <td class="px-5 py-4 hidden lg:table-cell">
                <p class="text-gray-400 text-xs">{{ $article->author->full_name ?? '—' }}</p>
              </td>
              <td class="px-5 py-4">
                @php
                  $badge = match($article->status) {
                    'published' => 'bg-green-500/15 text-green-400',
                    'review'    => 'bg-yellow-500/15 text-yellow-400',
                    'draft'     => 'bg-gray-500/15 text-gray-400',
                    'rejected'  => 'bg-red-500/15 text-red-400',
                    default     => 'bg-gray-500/15 text-gray-400',
                  };
                @endphp
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $badge }}">
                  {{ ucfirst($article->status) }}
                </span>
              </td>
              <td class="px-5 py-4 hidden lg:table-cell">
                <p class="text-gray-500 text-xs">{{ $article->created_at->format('M j, Y') }}</p>
              </td>
              <td class="px-5 py-4">
                <div class="flex items-center justify-end gap-2">
                  {{-- Publish (admin only, for review articles) --}}
                  @if($user->isAdmin() && $article->status === 'review')
                    <form action="{{ route('admin.blog.articles.publish', $article->id) }}" method="POST">
                      @csrf @method('PATCH')
                      <button type="submit" title="Publish"
                              class="p-1.5 text-green-400 hover:bg-green-500/10 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                      </button>
                    </form>
                  @endif

                  {{-- View public --}}
                  @if($article->status === 'published')
                    <a href="{{ route('blog.show', $article->slug) }}" target="_blank"
                       class="p-1.5 text-pm-cyan hover:bg-pm-cyan/10 rounded-lg transition-colors" title="View">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                      </svg>
                    </a>
                  @endif

                  {{-- Edit --}}
                  @if($user->isAdmin() || $article->author_id === $user->id)
                    <a href="{{ route('admin.blog.articles.edit', $article->id) }}"
                       class="p-1.5 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition-colors" title="Edit">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </a>
                  @endif

                  {{-- Delete --}}
                  @if($user->isAdmin() || $article->author_id === $user->id)
                    <form action="{{ route('admin.blog.articles.destroy', $article->id) }}" method="POST"
                          onsubmit="return confirm('Delete this article? This cannot be undone.')">
                      @csrf @method('DELETE')
                      <button type="submit" title="Delete"
                              class="p-1.5 text-gray-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @if($articles->hasPages())
      <div class="px-5 py-4 border-t border-white/5">
        {{ $articles->links() }}
      </div>
    @endif
  @else
    <div class="py-16 text-center">
      <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      <p class="text-gray-400">No articles found.</p>
      <a href="{{ route('admin.blog.articles.create') }}" class="text-pm-cyan text-sm hover:underline mt-2 block">Write your first article →</a>
    </div>
  @endif
</div>

@endsection