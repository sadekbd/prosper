@extends('layouts.public')

@section('meta_title',       ($article->meta_title ?? $article->title . ' — Prosper Media'))
@section('meta_description', ($article->meta_description ?? $article->excerpt))
@section('og_title',         ($article->og_title ?? $article->title))
@section('og_description',   ($article->og_description ?? $article->excerpt))
@if($article->og_image)
  @section('og_image', Storage::url($article->og_image))
@endif

@section('content')

{{-- ── Article Hero ─────────────────────────────────────────────── --}}
<section class="bg-hero-gradient py-16 relative overflow-hidden">
  <div class="absolute inset-0 grid-overlay pointer-events-none"></div>
  <div class="container-custom relative z-10 max-w-4xl mx-auto">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-8 flex-wrap">
      <a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a>
      <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
      </svg>
      <a href="{{ route('blog') }}" class="hover:text-white transition-colors">Blog</a>
      @if($article->category)
        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <a href="{{ route('blog', ['category' => $article->category->slug]) }}"
           class="hover:text-white transition-colors">{{ $article->category->name }}</a>
      @endif
    </nav>

    {{-- Category + meta --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
      @if($article->category)
        <span class="badge text-xs text-white font-semibold"
              style="background-color: {{ $article->category->color ?? '#00B4D8' }}cc; border-color: transparent;">
          {{ $article->category->name }}
        </span>
      @endif
      @if($article->read_time)
        <span class="text-gray-400 text-sm flex items-center gap-1.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          {{ $article->read_time }} min read
        </span>
      @endif
      <span class="text-gray-400 text-sm flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        {{ number_format($article->views) }} views
      </span>
    </div>

    {{-- Title --}}
    <h1 class="text-3xl md:text-4xl xl:text-5xl font-extrabold text-white font-heading leading-tight mb-6">
      {{ $article->title }}
    </h1>

    {{-- Excerpt --}}
    @if($article->excerpt)
      <p class="text-gray-300 text-xl leading-relaxed max-w-3xl">{{ $article->excerpt }}</p>
    @endif

    {{-- Author + date --}}
    <div class="flex items-center gap-4 mt-8 pt-8 border-t border-white/10">
      <div class="w-10 h-10 bg-pm-cyan/20 rounded-full flex items-center justify-center">
        <span class="text-pm-cyan font-extrabold text-sm font-heading">
          {{ strtoupper(substr($article->author->full_name ?? 'P', 0, 1)) }}
        </span>
      </div>
      <div>
        <p class="text-white font-semibold text-sm">{{ $article->author->full_name ?? 'Prosper Media' }}</p>
        <p class="text-gray-400 text-xs">
          Published {{ $article->published_at?->format('F j, Y') }}
          @if($article->published_at && $article->updated_at->gt($article->published_at->addDay()))
            · Updated {{ $article->updated_at->format('M j, Y') }}
          @endif
        </p>
      </div>
    </div>
  </div>
</section>

{{-- ── Article Body ─────────────────────────────────────────────── --}}
<section class="section-padding bg-white">
  <div class="container-custom">
    <div class="flex flex-col lg:flex-row gap-16 max-w-6xl mx-auto">

      {{-- ════════ MAIN ARTICLE ════════ --}}
      <article class="flex-1 min-w-0">

        {{-- Featured image --}}
        @if($article->featured_image)
          <div class="rounded-2xl overflow-hidden mb-10 shadow-sm">
            <img src="{{ Storage::url($article->featured_image) }}"
                 alt="{{ $article->title }}"
                 class="w-full h-64 md:h-96 object-cover">
          </div>
        @endif

        {{-- Article content --}}
        <div class="prose prose-slate prose-lg max-w-none
                    prose-headings:font-heading prose-headings:text-pm-navy prose-headings:font-extrabold
                    prose-h2:text-2xl prose-h2:mt-10 prose-h2:mb-4
                    prose-h3:text-xl prose-h3:mt-8 prose-h3:mb-3
                    prose-p:text-gray-600 prose-p:leading-relaxed prose-p:mb-5
                    prose-a:text-pm-cyan prose-a:no-underline hover:prose-a:underline prose-a:font-semibold
                    prose-strong:text-pm-navy prose-strong:font-bold
                    prose-ul:space-y-2 prose-li:text-gray-600
                    prose-ol:space-y-2
                    prose-table:text-sm
                    prose-thead:bg-pm-grey
                    prose-th:text-pm-navy prose-th:font-bold prose-th:p-3
                    prose-td:p-3 prose-td:text-gray-600
                    prose-blockquote:border-pm-cyan prose-blockquote:text-gray-600 prose-blockquote:bg-pm-grey/50 prose-blockquote:rounded-r-xl prose-blockquote:not-italic
                    prose-code:text-pm-cyan prose-code:bg-pm-grey prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded prose-code:text-sm prose-code:font-mono prose-code:before:content-none prose-code:after:content-none
                    prose-pre:bg-pm-navy prose-pre:text-gray-300 prose-pre:rounded-xl prose-pre:shadow-lg">
          {!! $article->content !!}
        </div>

        {{-- Tags --}}
        @if($article->tags && count($article->tags) > 0)
          <div class="mt-10 pt-8 border-t border-gray-100">
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Tags</p>
            <div class="flex flex-wrap gap-2">
              @foreach($article->tags as $tag)
                <span class="text-xs bg-pm-grey text-pm-slate border border-gray-200
                             px-3 py-1.5 rounded-lg font-medium hover:border-pm-cyan
                             hover:text-pm-cyan transition-colors cursor-default">
                  #{{ $tag }}
                </span>
              @endforeach
            </div>
          </div>
        @endif

        {{-- Share --}}
        <div class="mt-10 pt-8 border-t border-gray-100">
          <p class="text-sm font-bold text-pm-navy mb-4">Share this article</p>
          @php
            $shareUrl  = urlencode(url()->current());
            $shareText = urlencode($article->title . ' via Prosper Media');
          @endphp
          <div class="flex flex-wrap gap-3">
            <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareText }}"
               target="_blank" rel="noopener"
               class="flex items-center gap-2 px-4 py-2.5 bg-[#1DA1F2]/10 text-[#1DA1F2]
                      rounded-xl text-sm font-semibold hover:bg-[#1DA1F2]/20 transition-colors">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
              </svg>
              Share on Twitter
            </a>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}"
               target="_blank" rel="noopener"
               class="flex items-center gap-2 px-4 py-2.5 bg-[#0A66C2]/10 text-[#0A66C2]
                      rounded-xl text-sm font-semibold hover:bg-[#0A66C2]/20 transition-colors">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452z"/>
              </svg>
              Share on LinkedIn
            </a>
          </div>
        </div>

        {{-- Author bio --}}
        <div class="mt-10 bg-pm-grey rounded-2xl p-6 flex items-start gap-5">
          <div class="w-14 h-14 bg-pm-navy rounded-2xl flex items-center justify-center flex-shrink-0">
            <span class="text-pm-cyan font-extrabold text-xl font-heading">
              {{ strtoupper(substr($article->author->full_name ?? 'P', 0, 1)) }}
            </span>
          </div>
          <div>
            <p class="text-pm-navy font-extrabold font-heading mb-1">
              {{ $article->author->full_name ?? 'Prosper Media' }}
            </p>
            <p class="text-pm-cyan text-xs font-semibold uppercase tracking-wider mb-2">
              {{ ucfirst(str_replace('_', ' ', $article->author->role ?? 'author')) }}
            </p>
            <p class="text-gray-500 text-sm leading-relaxed">
              Technical marketing specialist at Prosper Media, focused on conversion tracking,
              Google Ads strategy and high-performance web development.
            </p>
          </div>
        </div>

        {{-- Article CTA --}}
        <div class="mt-10 bg-pm-navy rounded-2xl p-8 relative overflow-hidden">
          <div class="absolute inset-0 grid-overlay pointer-events-none rounded-2xl opacity-50"></div>
          <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
            <div>
              <p class="text-pm-gold text-xs font-bold uppercase tracking-widest mb-2">Prosper Media</p>
              <h3 class="text-white font-extrabold font-heading text-lg mb-1">
                Need Help Implementing This?
              </h3>
              <p class="text-gray-400 text-sm">
                Our technical team can set this up for your business. Book a free audit.
              </p>
            </div>
            <a href="{{ route('contact') }}" class="btn-primary flex-shrink-0">
              Contact Prosper Media
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
              </svg>
            </a>
          </div>
        </div>

      </article>

      {{-- ════════ SIDEBAR ════════ --}}
      <aside class="lg:w-72 flex-shrink-0 space-y-6">

        {{-- Article info --}}
        <div class="card">
          <h3 class="text-pm-navy font-extrabold font-heading mb-4 text-sm uppercase tracking-wider pb-3 border-b border-gray-100">
            Article Info
          </h3>
          <ul class="space-y-3 text-sm">
            <li class="flex items-center justify-between">
              <span class="text-gray-500">Published</span>
              <span class="text-pm-navy font-semibold">{{ $article->published_at?->format('M j, Y') }}</span>
            </li>
            @if($article->read_time)
              <li class="flex items-center justify-between">
                <span class="text-gray-500">Read Time</span>
                <span class="text-pm-navy font-semibold">{{ $article->read_time }} min</span>
              </li>
            @endif
            <li class="flex items-center justify-between">
              <span class="text-gray-500">Views</span>
              <span class="text-pm-navy font-semibold">{{ number_format($article->views) }}</span>
            </li>
            @if($article->category)
              <li class="flex items-center justify-between">
                <span class="text-gray-500">Category</span>
                <a href="{{ route('blog', ['category' => $article->category->slug]) }}"
                   class="font-semibold hover:text-pm-cyan transition-colors"
                   style="color: {{ $article->category->color ?? '#00B4D8' }}">
                  {{ $article->category->name }}
                </a>
              </li>
            @endif
          </ul>
        </div>

        {{-- Newsletter --}}
        <div class="bg-pm-navy rounded-2xl p-6 relative overflow-hidden">
          <div class="absolute inset-0 grid-overlay pointer-events-none rounded-2xl opacity-60"></div>
          <div class="relative z-10">
            <div class="w-10 h-10 bg-pm-cyan/10 rounded-xl flex items-center justify-center mb-4">
              <svg class="w-5 h-5 text-pm-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
            </div>
            <h3 class="text-white font-extrabold font-heading mb-2 text-sm">Weekly Insights</h3>
            <p class="text-gray-400 text-xs leading-relaxed mb-4">Get articles like this in your inbox every week.</p>
            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-2">
              @csrf
              <input type="email" name="email" placeholder="your@email.com" required
                     class="w-full bg-white/10 border border-white/10 rounded-xl px-3 py-2.5 text-white
                            placeholder-gray-500 text-xs focus:outline-none focus:border-pm-cyan transition-colors">
              <button type="submit" class="btn-primary w-full justify-center text-xs py-2.5">
                Subscribe Free
              </button>
            </form>
          </div>
        </div>

        {{-- Work with us --}}
        <div class="bg-pm-gold/10 border border-pm-gold/20 rounded-2xl p-5">
          <div class="text-2xl mb-3">⚡</div>
          <h3 class="text-pm-navy font-extrabold font-heading mb-2 text-sm">Work With Us</h3>
          <p class="text-gray-500 text-xs leading-relaxed mb-4">
            Need help implementing this for your business? We offer free audits.
          </p>
          <a href="{{ route('contact') }}" class="btn-primary text-xs w-full justify-center py-2.5">
            Get a Free Audit
          </a>
        </div>

        {{-- Services --}}
        <div class="card">
          <h3 class="text-pm-navy font-extrabold font-heading mb-4 text-sm uppercase tracking-wider pb-3 border-b border-gray-100">
            Our Services
          </h3>
          <ul class="space-y-2">
            @foreach([
              ['Google Ads Mastery',          'google-ads-mastery'],
              ['Conversion Tracking',         'advanced-conversion-tracking'],
              ['Web Development',             'professional-web-development'],
            ] as [$svcName, $svcSlug])
              <li>
                <a href="{{ route('services.show', $svcSlug) }}"
                   class="flex items-center gap-2 text-sm text-pm-slate hover:text-pm-cyan
                          transition-colors py-1.5 group">
                  <svg class="w-3 h-3 text-pm-cyan/40 group-hover:text-pm-cyan transition-colors flex-shrink-0"
                       fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                  </svg>
                  {{ $svcName }}
                </a>
              </li>
            @endforeach
          </ul>
        </div>

      </aside>
    </div>
  </div>
</section>

{{-- ── Related Articles ─────────────────────────────────────────── --}}
@if($related->count() > 0)
<section class="section-padding bg-pm-grey">
  <div class="container-custom">
    <h2 class="text-2xl font-extrabold text-pm-navy font-heading mb-10">Related Articles</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      @foreach($related as $rel)
        <article class="card group hover:-translate-y-1 transition-all duration-300">
          <div class="h-36 rounded-xl mb-4 overflow-hidden bg-gradient-to-br from-pm-cyan/8 to-pm-navy/10 flex items-center justify-center">
            @if($rel->featured_image)
              <img src="{{ Storage::url($rel->featured_image) }}" alt="{{ $rel->title }}"
                   class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            @else
              <svg class="w-10 h-10 text-pm-cyan/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            @endif
          </div>

          <div class="flex items-center gap-2 mb-2">
            @if($rel->category)
              <span class="badge text-[10px] text-white font-semibold"
                    style="background-color: {{ $rel->category->color ?? '#00B4D8' }}cc; border-color: transparent;">
                {{ $rel->category->name }}
              </span>
            @endif
            @if($rel->read_time)
              <span class="text-[10px] text-gray-400">{{ $rel->read_time }} min</span>
            @endif
          </div>

          <h3 class="text-pm-navy font-extrabold font-heading text-sm leading-snug mb-3
                     group-hover:text-pm-cyan transition-colors">{{ $rel->title }}</h3>

          <a href="{{ route('blog.show', $rel->slug) }}"
             class="text-pm-cyan text-xs font-bold hover:text-pm-navy transition-colors">
            Read Article →
          </a>
        </article>
      @endforeach
    </div>
  </div>
</section>
@endif

@endsection