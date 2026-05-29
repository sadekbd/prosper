@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Welcome back, ' . auth('admin')->user()->full_name)

@section('content')

@php $user = auth('admin')->user(); @endphp

{{-- ── Greeting ──────────────────────────────────────────────────── --}}
<div class="mb-8">
  <h2 class="text-xl font-extrabold text-white font-heading">
    Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
    {{ explode(' ', $user->full_name)[0] }} 👋
  </h2>
  <p class="text-gray-400 text-sm mt-1">
    Here's what's happening with Prosper Media today.
    {{ now()->format('l, F j, Y') }}
  </p>
</div>

{{-- ── Stat Cards ────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

  @php
    $cards = [
      ['Total Articles',     $stats['total_articles'],     'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', '#00B4D8', 'Published: '.$stats['published_articles']],
      ['New Messages',       $stats['new_messages'],       'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', '#FF4444', 'Total: '.$stats['total_messages']],
      ['Portfolio Projects', $stats['total_portfolio'],    'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', '#FFB703', 'Published cases'],
      ['Active Services',    $stats['active_services'],    'M13 10V3L4 14h7v7l9-11h-7z', '#10B981', 'In the system'],
      ['Article Writers',    $stats['total_writers'],      'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', '#8B5CF6', 'Registered / 10 max'],
      ['In Review',          $stats['review_articles'],    'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', '#F59E0B', 'Awaiting publish'],
      ['Drafts',             $stats['draft_articles'],     'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', '#6B7280', 'In progress'],
      ['Subscribers',        $stats['subscribers'],        'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', '#00B4D8', 'Newsletter list'],
    ];

    // Article writers see fewer cards
    if ($user->role === 'article_writer') {
      $cards = array_slice($cards, 0, 3);
    }
  @endphp

  @foreach($cards as [$label, $value, $icon, $color, $sub])
    <div class="bg-[#1a2540] border border-white/5 rounded-2xl p-5 hover:border-white/10 transition-colors">
      <div class="flex items-start justify-between mb-4">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center"
             style="background-color: {{ $color }}20;">
          <svg class="w-5 h-5" style="color: {{ $color }}"
               fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
          </svg>
        </div>
        <span class="text-2xl font-extrabold font-heading" style="color: {{ $color }}">
          {{ number_format($value) }}
        </span>
      </div>
      <p class="text-white text-sm font-semibold mb-0.5">{{ $label }}</p>
      <p class="text-gray-500 text-xs">{{ $sub }}</p>
    </div>
  @endforeach

</div>

{{-- ── Tables Row ────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

  {{-- Recent Contact Messages --}}
  @if(in_array($user->role, ['super_admin', 'admin']))
  <div class="bg-[#1a2540] border border-white/5 rounded-2xl overflow-hidden">
    <div class="flex items-center justify-between px-6 py-5 border-b border-white/5">
      <div>
        <h3 class="text-white font-extrabold font-heading text-sm">Recent Messages</h3>
        <p class="text-gray-500 text-xs mt-0.5">Latest contact form submissions</p>
      </div>
      <a href="{{ route('admin.messages') }}"
         class="text-pm-cyan text-xs font-semibold hover:underline">
        View all →
      </a>
    </div>

    @if($recentMessages->count() > 0)
      <div class="divide-y divide-white/5">
        @foreach($recentMessages as $msg)
          <div class="px-6 py-4 hover:bg-white/3 transition-colors">
            <div class="flex items-start justify-between gap-3">
              <div class="min-w-0">
                <div class="flex items-center gap-2 mb-1">
                  <p class="text-white text-sm font-semibold truncate">{{ $msg->name }}</p>
                  @if($msg->status === 'new')
                    <span class="flex-shrink-0 w-2 h-2 bg-red-500 rounded-full"></span>
                  @endif
                </div>
                <p class="text-gray-400 text-xs truncate">{{ $msg->email }}</p>
                <p class="text-gray-500 text-xs mt-0.5">
                  {{ ucfirst(str_replace('_',' ',$msg->service_interest)) }}
                </p>
              </div>
              <div class="flex-shrink-0 text-right">
                <span class="inline-block px-2 py-0.5 rounded-lg text-[10px] font-semibold
                  {{ $msg->status === 'new'     ? 'bg-red-500/10 text-red-400' :
                     ($msg->status === 'read'   ? 'bg-blue-500/10 text-blue-400' :
                     ($msg->status === 'replied' ? 'bg-green-500/10 text-green-400' :
                      'bg-gray-500/10 text-gray-400')) }}">
                  {{ ucfirst($msg->status) }}
                </span>
                <p class="text-gray-600 text-[10px] mt-1">{{ $msg->created_at->diffForHumans() }}</p>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="px-6 py-12 text-center">
        <svg class="w-10 h-10 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        <p class="text-gray-500 text-sm">No messages yet</p>
      </div>
    @endif
  </div>
  @endif

  {{-- Recent Articles --}}
  <div class="bg-[#1a2540] border border-white/5 rounded-2xl overflow-hidden">
    <div class="flex items-center justify-between px-6 py-5 border-b border-white/5">
      <div>
        <h3 class="text-white font-extrabold font-heading text-sm">
          {{ $user->role === 'article_writer' ? 'My Articles' : 'Recent Articles' }}
        </h3>
        <p class="text-gray-500 text-xs mt-0.5">Latest blog content</p>
      </div>
      <a href="{{ route('admin.blog.articles') }}"
         class="text-pm-cyan text-xs font-semibold hover:underline">
        View all →
      </a>
    </div>

    @if($recentArticles->count() > 0)
      <div class="divide-y divide-white/5">
        @foreach($recentArticles as $article)
          <div class="px-6 py-4 hover:bg-white/3 transition-colors">
            <div class="flex items-start justify-between gap-3">
              <div class="min-w-0">
                <p class="text-white text-sm font-semibold truncate">{{ $article->title }}</p>
                @if($article->category)
                  <p class="text-xs mt-0.5" style="color: {{ $article->category->color ?? '#00B4D8' }}">
                    {{ $article->category->name }}
                  </p>
                @endif
                <p class="text-gray-600 text-[10px] mt-1">{{ $article->updated_at->diffForHumans() }}</p>
              </div>
              <span class="flex-shrink-0 inline-block px-2 py-0.5 rounded-lg text-[10px] font-semibold
                {{ $article->status === 'published' ? 'bg-green-500/10 text-green-400' :
                   ($article->status === 'review'   ? 'bg-yellow-500/10 text-yellow-400' :
                   ($article->status === 'rejected' ? 'bg-red-500/10 text-red-400' :
                    'bg-gray-500/10 text-gray-400')) }}">
                {{ ucfirst($article->status) }}
              </span>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="px-6 py-12 text-center">
        <svg class="w-10 h-10 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-gray-500 text-sm">No articles yet</p>
        <a href="{{ route('admin.blog.articles.create') }}" class="text-pm-cyan text-xs hover:underline mt-2 block">
          Write your first article →
        </a>
      </div>
    @endif
  </div>

</div>

{{-- ── Quick Actions ─────────────────────────────────────────────── --}}
<div class="mt-6 bg-[#1a2540] border border-white/5 rounded-2xl p-6">
  <h3 class="text-white font-extrabold font-heading text-sm mb-5">Quick Actions</h3>
  <div class="flex flex-wrap gap-3">

    @if(Route::has('admin.blog.articles.create'))
    <a href="{{ route('admin.blog.articles.create') }}"
       class="flex items-center gap-2 bg-pm-cyan/10 text-pm-cyan border border-pm-cyan/20
              px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-pm-cyan/20 transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      New Article
    </a>
    @endif

    @if(in_array($user->role, ['super_admin','admin']) && Route::has('admin.portfolio.create'))
    <a href="{{ route('admin.portfolio.create') }}"
       class="flex items-center gap-2 bg-pm-gold/10 text-pm-gold border border-pm-gold/20
              px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-pm-gold/20 transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      Add Project
    </a>
    @endif

    @if(in_array($user->role, ['super_admin','admin']) && Route::has('admin.messages'))
    <a href="{{ route('admin.messages') }}"
       class="flex items-center gap-2 bg-white/5 text-gray-300 border border-white/10
              px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-white/10 transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
      </svg>
      View Messages
      @if($stats['new_messages'] > 0)
        <span class="bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">
          {{ $stats['new_messages'] }}
        </span>
      @endif
    </a>
    @endif

    @if($user->role === 'super_admin' && Route::has('admin.users'))
    <a href="{{ route('admin.users') }}"
       class="flex items-center gap-2 bg-white/5 text-gray-300 border border-white/10
              px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-white/10 transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
      </svg>
      Manage Users
    </a>
    @endif

    @if($user->role === 'super_admin' && Route::has('admin.settings'))
    <a href="{{ route('admin.settings') }}"
       class="flex items-center gap-2 bg-white/5 text-gray-300 border border-white/10
              px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-white/10 transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
      </svg>
      Site Settings
    </a>
    @endif

    <a href="{{ route('home') }}" target="_blank"
       class="flex items-center gap-2 bg-white/5 text-gray-300 border border-white/10
              px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-white/10 transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
      </svg>
      View Website
    </a>

  </div>
</div>

@endsection