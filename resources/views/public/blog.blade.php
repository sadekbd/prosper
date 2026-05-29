@extends('layouts.public')

@section('meta_title',       'Blog & Articles — Google Ads Tips, Tracking Guides & Web Dev Tutorials')
@section('meta_description', 'Practical guides on Google Ads, conversion tracking, GTM and Laravel web development from the Prosper Media technical team.')

@section('content')

{{-- Hero --}}
<section class="bg-hero-gradient py-20 relative overflow-hidden">
  <div class="absolute inset-0 grid-overlay pointer-events-none"></div>
  <div class="container-custom relative z-10 text-center max-w-3xl mx-auto">
    <p class="section-label mb-4">Knowledge Hub</p>
    <h1 class="text-4xl md:text-5xl font-extrabold text-white font-heading leading-tight mb-5">
      Insights, Tutorials &
      <span class="text-gradient"> Technical Guides</span>
    </h1>
    <p class="text-gray-300 text-xl">Practical knowledge written for marketers and developers who want real answers.</p>
  </div>
</section>

<section class="section-padding bg-pm-grey">
  <div class="container-custom">
    <div class="flex flex-col lg:flex-row gap-12">

      {{-- ── MAIN CONTENT ── --}}
      <div class="flex-1 min-w-0">

        {{-- Search & Filter --}}
        <form method="GET" action="{{ route('blog') }}" class="flex flex-col sm:flex-row gap-4 mb-10">
          <div class="relative flex-1">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search"
                   value="{{ $search ?? '' }}"
                   placeholder="Search articles..."
                   class="input-field pl-11 bg-white">
          </div>
          <button type="submit" class="btn-primary text-sm px-6">Search</button>
          @if($search)
            <a href="{{ route('blog') }}" class="btn-outline text-sm px-5">Clear</a>
          @endif
        </form>

        {{-- Category tabs --}}
        @if(isset($categories) && $categories->count() > 0)
          <div class="flex flex-wrap gap-2 mb-10">
            <a href="{{ route('blog') }}"
               class="px-4 py-2 rounded-xl text-xs font-semibold border transition-all duration-200
                      {{ !$activeCategory ? 'bg-pm-navy text-white border-pm-navy' : 'bg-white text-pm-slate border-gray-200 hover:border-pm-cyan hover:text-pm-cyan' }}">
              All ({{ $categories->sum('published_count') }})
            </a>
            @foreach($categories as $cat)
              <a href="{{ route('blog', ['category' => $cat->slug]) }}"
                 class="px-4 py-2 rounded-xl text-xs font-semibold border transition-all duration-200
                        {{ $activeCategory?->id === $cat->id ? 'bg-pm-navy text-white border-pm-navy' : 'bg-white text-pm-slate border-gray-200 hover:border-pm-cyan hover:text-pm-cyan' }}">
                {{ $cat->name }} ({{ $cat->published_count }})
              </a>
            @endforeach
          </div>
        @endif

        {{-- ── DB Articles ── --}}
        @if(isset($articles) && $articles->count() > 0)
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($articles as $article)
              <article class="card group hover:-translate-y-1 transition-all duration-300">
                <div class="h-40 rounded-xl mb-5 overflow-hidden bg-gradient-to-br from-pm-cyan/8 to-pm-navy/10 flex items-center justify-center relative">
                  @if($article->featured_image)
                    <img src="{{ Storage::url($article->featured_image) }}"
                         alt="{{ $article->title }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                  @else
                    <svg class="w-12 h-12 text-pm-cyan/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                  @endif
                </div>

                <div class="flex items-center gap-3 mb-3">
                  @if($article->category)
                    <span class="badge text-[10px] text-white font-semibold"
                          style="background-color: {{ $article->category->color ?? '#00B4D8' }}cc; border-color: transparent;">
                      {{ $article->category->name }}
                    </span>
                  @endif
                  <span class="text-xs text-gray-400">{{ $article->published_at?->format('M j, Y') }}</span>
                  @if($article->read_time)
                    <span class="text-xs text-gray-400">· {{ $article->read_time }} min read</span>
                  @endif
                </div>

                <h2 class="text-pm-navy font-extrabold font-heading text-base leading-snug mb-3
                           group-hover:text-pm-cyan transition-colors">
                  <a href="{{ route('blog.show', $article->slug) }}">{{ $article->title }}</a>
                </h2>
                <p class="text-gray-500 text-sm leading-relaxed mb-5 line-clamp-3">{{ $article->excerpt }}</p>

                <div class="flex items-center justify-between">
                  <a href="{{ route('blog.show', $article->slug) }}"
                     class="inline-flex items-center gap-2 text-pm-cyan text-sm font-bold hover:text-pm-navy transition-colors">
                    Read Article
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                  </a>
                  <span class="text-xs text-gray-400 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{ number_format($article->views) }}
                  </span>
                </div>
              </article>
            @endforeach
          </div>

          {{-- Pagination --}}
          @if($articles->hasPages())
            <div class="mt-12">
              {{ $articles->links() }}
            </div>
          @endif

        @else
          {{-- Static fallback when DB is empty --}}
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach([
              ['tracking-guides',     '#F57C00', 'How to Set Up Server-Side Conversion Tracking in Google Ads 2025',   'A complete guide to GTM server containers, transport URL setup, and server-side conversion data for maximum accuracy.',       'May 15, 2025', '12 min'],
              ['tracking-guides',     '#F57C00', 'Server-Side vs Client-Side Tracking: The Complete 2025 Comparison',  'Understanding when client-side pixels fail and how server-to-server APIs recover your attribution accuracy.',                 'May 8, 2025',  '9 min'],
              ['google-ads-tips',     '#4285F4', 'Google Ads Smart Bidding: When to Use It and When to Avoid It',      'The honest guide — data thresholds, campaign conditions, and when manual bidding actually beats automated strategies.',      'Apr 15, 2025', '10 min'],
              ['web-dev-tutorials',   '#FF2D20', 'Building High-Converting Laravel Landing Pages: A Technical Guide',   'The complete technical framework behind landing pages that convert at 3x industry average, built with Laravel and Tailwind.',  'Apr 2, 2025',  '15 min'],
            ] as [$catSlug, $color, $title, $excerpt, $date, $read])
              <article class="card group hover:-translate-y-1 transition-all duration-300">
                <div class="h-40 rounded-xl mb-5 flex items-center justify-center"
                     style="background: linear-gradient(135deg, {{ $color }}12, {{ $color }}05);">
                  <svg class="w-12 h-12 opacity-20" style="color: {{ $color }}"
                       fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                </div>
                <div class="flex items-center gap-3 mb-3">
                  <span class="badge text-[10px] text-white"
                        style="background-color: {{ $color }}cc; border-color: transparent;">
                    {{ ucwords(str_replace('-', ' ', $catSlug)) }}
                  </span>
                  <span class="text-xs text-gray-400">{{ $date }}</span>
                  <span class="text-xs text-gray-400">· {{ $read }} read</span>
                </div>
                <h2 class="text-pm-navy font-extrabold font-heading text-base leading-snug mb-3 group-hover:text-pm-cyan transition-colors">{{ $title }}</h2>
                <p class="text-gray-500 text-sm leading-relaxed mb-5">{{ $excerpt }}</p>
                <a href="{{ route('blog') }}" class="inline-flex items-center gap-2 text-pm-cyan text-sm font-bold hover:text-pm-navy transition-colors">
                  Read Article
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                  </svg>
                </a>
              </article>
            @endforeach
          </div>
        @endif

      </div>

      {{-- ── SIDEBAR ── --}}
      <aside class="lg:w-80 space-y-6 flex-shrink-0">

        {{-- Newsletter --}}
        <div class="bg-pm-navy rounded-2xl p-6 relative overflow-hidden">
          <div class="absolute inset-0 grid-overlay pointer-events-none rounded-2xl opacity-60"></div>
          <div class="relative z-10">
            <div class="w-10 h-10 bg-pm-cyan/10 rounded-xl flex items-center justify-center mb-4">
              <svg class="w-5 h-5 text-pm-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
            </div>
            <h3 class="text-white font-extrabold font-heading mb-2">Weekly Insights</h3>
            <p class="text-gray-400 text-xs leading-relaxed mb-4">Technical guides every week. No spam, unsubscribe any time.</p>
            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-3">
              @csrf
              <input type="email" name="email" placeholder="your@email.com" required
                     class="w-full bg-white/10 border border-white/10 rounded-xl px-4 py-3 text-white
                            placeholder-gray-500 text-sm focus:outline-none focus:border-pm-cyan transition-colors">
              <button type="submit" class="btn-primary w-full justify-center text-sm py-3">Subscribe Free</button>
            </form>
          </div>
        </div>

        {{-- Work with us --}}
        <div class="bg-pm-gold/10 border border-pm-gold/20 rounded-2xl p-6">
          <div class="text-2xl mb-3">⚡</div>
          <h3 class="text-pm-navy font-extrabold font-heading mb-2">Work With Us</h3>
          <p class="text-gray-500 text-sm leading-relaxed mb-4">Need help implementing any of these strategies for your business?</p>
          <a href="{{ route('contact') }}" class="btn-primary text-sm w-full justify-center">Get a Free Audit</a>
        </div>

        {{-- Popular posts --}}
        @if(isset($popularPosts) && $popularPosts->count() > 0)
          <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h3 class="text-pm-navy font-extrabold font-heading mb-5 pb-3 border-b border-gray-100">Popular Articles</h3>
            <div class="space-y-4">
              @foreach($popularPosts as $pop)
                <a href="{{ route('blog.show', $pop->slug) }}"
                   class="flex items-start gap-3 group hover:bg-pm-grey p-2 -mx-2 rounded-xl transition-colors">
                  <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"
                       style="background-color: {{ $pop->category?->color ?? '#00B4D8' }}20;">
                    <svg class="w-4 h-4" style="color: {{ $pop->category?->color ?? '#00B4D8' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-pm-slate text-xs font-semibold leading-snug group-hover:text-pm-cyan transition-colors">{{ $pop->title }}</p>
                    <p class="text-gray-400 text-[10px] mt-0.5">{{ number_format($pop->views) }} views</p>
                  </div>
                </a>
              @endforeach
            </div>
          </div>
        @endif

        {{-- Categories sidebar --}}
        @if(isset($categories) && $categories->count() > 0)
          <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h3 class="text-pm-navy font-extrabold font-heading mb-5 pb-3 border-b border-gray-100">Categories</h3>
            <ul class="space-y-2">
              @foreach($categories as $cat)
                <li>
                  <a href="{{ route('blog', ['category' => $cat->slug]) }}"
                     class="flex items-center justify-between py-2 px-3 rounded-xl hover:bg-pm-grey transition-colors group">
                    <div class="flex items-center gap-2">
                      <div class="w-2 h-2 rounded-full" style="background-color: {{ $cat->color }}"></div>
                      <span class="text-pm-slate text-sm group-hover:text-pm-cyan transition-colors">{{ $cat->name }}</span>
                    </div>
                    <span class="text-[10px] bg-pm-grey text-gray-400 px-2 py-0.5 rounded-full font-medium">{{ $cat->published_count }}</span>
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
        @endif

      </aside>
    </div>
  </div>
</section>

@endsection

{{-- Below is the old services single page code for reference when building out portfolio single pages --}}

{{-- Service details --}}