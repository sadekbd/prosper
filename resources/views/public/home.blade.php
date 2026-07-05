@extends('layouts.public')

@section('meta_title',       'Prosper Media — Engineering Digital Success with Technical Precision')
@section('meta_description', 'Tech-first Google Ads management, conversion tracking, AI automation and high-performance web development. ROI-focused digital growth.')

@section('content')

@php
  $homepageContent = new \App\Support\HomepageContent();
  $homeSections = $homeSections ?? collect();
@endphp

{{-- ═══════════════════════════════════════════════════════════════
     HERO
════════════════════════════════════════════════════════════════ --}}
@php
  $heroContent = $homepageContent->hero($homeSections->get('hero'));
@endphp
<section class="bg-hero-gradient min-h-[calc(100vh-80px)] flex items-center relative overflow-hidden">

  {{-- Grid overlay --}}
  <div class="absolute inset-0 grid-overlay opacity-100 pointer-events-none"></div>

  {{-- Glow orbs --}}
  <div class="absolute top-1/3 left-1/4   w-80 h-80 bg-pm-cyan/8  rounded-full blur-3xl pointer-events-none"></div>
  <div class="absolute bottom-1/3 right-1/4 w-72 h-72 bg-pm-gold/5  rounded-full blur-3xl pointer-events-none"></div>

  <div class="container-custom relative z-10 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

      {{-- ── Left: Text ── --}}
      <div>
        {{-- Slogan badge --}}
        <div class="inline-flex items-center gap-2 border border-pm-gold/30 bg-pm-gold/10
                    text-pm-gold rounded-full px-4 py-2 text-xs font-bold uppercase tracking-widest mb-8">
          <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
          </svg>
          {{ $heroContent['eyebrow'] }}
        </div>

        <h1 class="text-4xl md:text-5xl xl:text-6xl font-extrabold text-white leading-[1.1] mb-6 font-heading">
          {{ $heroContent['title_lines'][0] }}
          <br>
          <span class="text-gradient">{{ $heroContent['title_lines'][1] }}</span> {{ $heroContent['title_lines'][2] }}
          <br>
          {{ $heroContent['title_lines'][3] }}
        </h1>

        <p class="text-gray-300 text-lg md:text-xl leading-relaxed mb-10 max-w-lg">
          {{ $heroContent['subtitle'] }}
        </p>

        <div class="flex flex-wrap gap-4 mb-12">
          <a href="{{ $heroContent['primary_url'] }}" class="btn-primary px-8 py-4 text-base">
            {{ $heroContent['primary_label'] }}
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
          </a>
          <a href="{{ $heroContent['secondary_url'] }}" class="btn-secondary px-8 py-4 text-base">
            {{ $heroContent['secondary_label'] }}
          </a>
        </div>

        {{-- Stats --}}
        @php
          $statsContent = $homepageContent->stats($homeSections->get('stats'));
          $statsItems = collect($statsContent['items']);
        @endphp
        <div class="flex flex-wrap gap-6 pt-8 border-t border-white/10">
          @foreach($statsItems as $stat)
            <div>
              <p class="text-2xl font-extrabold text-pm-cyan font-heading"
                 data-counter="{{ $stat['value'] }}" data-suffix="{{ $stat['suffix'] }}">{{ $stat['value'] }}{{ $stat['suffix'] }}@if($stat['sep'] !== '') {{ $stat['sep'] }}@endif</p>
              <p class="text-xs text-gray-400 mt-0.5">{{ $stat['label'] }}</p>
            </div>
          @endforeach
        </div>
      </div>

      {{-- ── Right: Dashboard Mockup ── --}}
      <div class="hidden lg:block relative">
        <div class="relative bg-white/5 border border-white/10 rounded-2xl p-6
                    backdrop-blur-sm glow-cyan animate-float">

          {{-- Mockup header --}}
          <div class="flex items-center justify-between mb-5 pb-4 border-b border-white/10">
            <div>
              <p class="text-[10px] text-gray-500 uppercase tracking-widest">{{ $heroContent['dashboard']['eyebrow'] }}</p>
              <p class="text-white font-bold font-heading">{{ $heroContent['dashboard']['title'] }}</p>
            </div>
            <span class="flex items-center gap-1.5 text-xs text-green-400 font-medium">
              <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
              {{ $heroContent['dashboard']['status'] }}
            </span>
          </div>

          {{-- KPI row --}}
          <div class="grid grid-cols-3 gap-3 mb-5">
            @foreach([
              ['Conversions', '1,248', '↑ 34%', 'text-pm-cyan'],
              ['ROAS',        '4.8x',  '↑ 12%', 'text-pm-gold'],
              ['CTR',         '7.2%',  '↑ 8%',  'text-white'],
            ] as [$kpi, $val, $chg, $col])
              <div class="bg-pm-navy/60 rounded-xl p-3 border border-white/5">
                <p class="text-[10px] text-gray-500 uppercase mb-1">{{ $heroContent['dashboard']['metrics'][$loop->index]['label'] }}</p>
                <p class="text-lg font-bold font-heading {{ $heroContent['dashboard']['metrics'][$loop->index]['color'] }}">{{ $heroContent['dashboard']['metrics'][$loop->index]['value'] }}</p>
                <p class="text-[10px] text-green-400 mt-0.5">{{ $heroContent['dashboard']['metrics'][$loop->index]['change'] }}</p>
              </div>
            @endforeach
          </div>

          {{-- Bar chart --}}
          <div class="mb-4">
            <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-3">{{ $heroContent['dashboard']['chart_label'] }}</p>
            <div class="flex items-end gap-1.5 h-16">
              @foreach([35, 55, 42, 70, 60, 85, 75] as $h)
                <div class="flex-1 rounded-t bg-pm-cyan/25 hover:bg-pm-cyan/50 transition-colors"
                     style="height:{{ $heroContent['dashboard']['bar_heights'][$loop->index] }}%"></div>
              @endforeach
            </div>
            <div class="flex justify-between mt-1.5">
              @foreach(['Mo','Tu','We','Th','Fr','Sa','Su'] as $d)
                <span class="text-[9px] text-gray-600 flex-1 text-center">{{ $heroContent['dashboard']['chart_days'][$loop->index] }}</span>
              @endforeach
            </div>
          </div>

          {{-- Tracking tags --}}
          <div class="flex flex-wrap items-center gap-2 pt-4 border-t border-white/5">
            <p class="text-[10px] text-gray-500 mr-1">{{ $heroContent['dashboard']['tracking_label'] }}</p>
            @foreach($heroContent['dashboard']['tracking_items'] as $tag)
              <span class="text-[10px] bg-pm-cyan/10 text-pm-cyan px-2 py-0.5 rounded-md font-medium">{{ $tag }}</span>
            @endforeach
          </div>
        </div>

        {{-- Floating badges --}}
        <div class="absolute -top-4 -right-4 bg-pm-gold text-pm-navy rounded-xl px-4 py-2
                    text-sm font-extrabold shadow-xl font-heading shadow-yellow-500/20">
          {{ trim(($heroContent['dashboard']['top_badge']['label'] ?? 'ROI') . ' ' . ($heroContent['dashboard']['top_badge']['value'] ?? '↑ 340%')) }}
        </div>
        <div class="absolute -bottom-4 -left-4 bg-white text-pm-navy rounded-xl px-4 py-2
                    text-xs font-semibold shadow-xl flex items-center gap-2">
          <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
          {{ $heroContent['dashboard']['bottom_badge']['label'] ?? 'Tracking Active' }}
        </div>
      </div>

    </div>
  </div>

  {{-- Scroll cue --}}
  <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce opacity-30">
    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
    </svg>
  </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     TRUST BAR
════════════════════════════════════════════════════════════════ --}}
@php
  $trustBarContent = $homepageContent->trustBar($homeSections->get('trust_bar'));
  $trustBarItems = collect($trustBarContent['items']);
@endphp
<section class="bg-white border-y border-gray-100 py-12">
  <div class="container-custom">
    <p class="text-center text-[10px] text-gray-400 uppercase tracking-[0.3em] font-semibold mb-8">
      {{ $trustBarContent['title'] }}
    </p>
    <div class="flex flex-wrap items-center justify-center gap-6 md:gap-12">
      @foreach($trustBarItems as $item)
        <div class="flex items-center gap-2 text-gray-400 hover:text-pm-slate
                    transition-all duration-200 group cursor-default">
          <div class="w-2.5 h-2.5 rounded-full group-hover:scale-125 transition-transform"
               style="background-color: {{ $item['color'] }}"></div>
          <span class="text-sm font-semibold tracking-wide">{{ $item['label'] }}</span>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     WHY CHOOSE US
════════════════════════════════════════════════════════════════ --}}
@php
  $differenceContent = $homepageContent->difference($homeSections->get('difference'));
  $differenceCards = collect($differenceContent['cards']);
@endphp
<section class="section-padding bg-pm-grey">
  <div class="container-custom">

    <div class="text-center mb-16 aos">
      <p class="section-label mb-3">{{ $differenceContent['eyebrow'] }}</p>
      <h2 class="section-title">{{ $differenceContent['title'] }}</h2>
      <p class="text-gray-500 text-lg mt-4 max-w-2xl mx-auto">
        {{ $differenceContent['subtitle'] }}
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      @foreach($differenceCards as $p)
        <div class="card-hover aos group relative overflow-hidden">
          {{-- Number watermark --}}
          <div class="absolute top-4 right-5 text-6xl font-extrabold
                      {{ $p['color'] === 'gold' ? 'text-pm-gold/5' : 'text-pm-cyan/5' }}
                      font-heading select-none">
            {{ substr($p['badge'], 4) }}
          </div>

          <div class="relative z-10">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6
                        {{ $p['color'] === 'gold' ? 'bg-pm-gold/10' : 'bg-pm-cyan/10' }}">
              <svg class="w-7 h-7 {{ $p['color'] === 'gold' ? 'text-pm-gold' : 'text-pm-cyan' }}"
                   fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $p['icon'] }}"/>
              </svg>
            </div>

            <span class="text-[10px] font-bold uppercase tracking-widest
                         {{ $p['color'] === 'gold' ? 'text-pm-gold' : 'text-pm-cyan' }} mb-2 block">
              {{ $p['badge'] }}
            </span>
            <h3 class="text-xl font-extrabold text-pm-navy font-heading mb-3">{{ $p['title'] }}</h3>
            <p class="text-gray-500 text-sm leading-relaxed">{{ $p['desc'] }}</p>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SERVICES PREVIEW
════════════════════════════════════════════════════════════════ --}}
@php
  $servicesIntroContent = $homepageContent->servicesIntro($homeSections->get('services_intro'));
@endphp
<section class="section-padding bg-white">
  <div class="container-custom">

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16">
      <div class="aos">
        <p class="section-label mb-3">{{ $servicesIntroContent['eyebrow'] }}</p>
        <h2 class="section-title">{{ $servicesIntroContent['title'] }}</h2>
        <p class="text-gray-500 text-lg mt-4 max-w-xl">
          {{ $servicesIntroContent['subtitle'] }}
        </p>
      </div>
      <a href="{{ $servicesIntroContent['cta_url'] }}" class="btn-outline text-sm self-start shrink-0">
        {{ $servicesIntroContent['cta_label'] }}
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
      </a>
    </div>

    @php
      $coreServices = $homepageContent->serviceCards($services ?? collect());
      $icons = $servicesIntroContent['card_icons'];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      @foreach($coreServices as $i => $svc)
        <div class="group relative bg-pm-grey rounded-2xl p-8 hover:bg-pm-navy
                    transition-all duration-500 aos cursor-pointer overflow-hidden">
          {{-- Hover glow --}}
          <div class="absolute inset-0 bg-gradient-to-br from-pm-cyan/10 to-transparent
                      opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-2xl"></div>

          <div class="relative z-10">
            <div class="w-14 h-14 bg-pm-cyan/10 group-hover:bg-pm-cyan/20 rounded-2xl
                        flex items-center justify-center mb-6 transition-colors duration-300">
              <svg class="w-7 h-7 text-pm-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$i] ?? $icons[0] }}"/>
              </svg>
            </div>

            <div class="text-[10px] font-bold text-pm-cyan/60 group-hover:text-pm-cyan/40
                        uppercase tracking-widest mb-2 transition-colors">0{{ $i+1 }}</div>
            <h3 class="text-xl font-extrabold text-pm-navy group-hover:text-white
                       font-heading mb-3 transition-colors duration-300">{{ $svc->title }}</h3>
            <p class="text-gray-500 group-hover:text-gray-300 text-sm leading-relaxed mb-6
                      transition-colors duration-300">{{ $svc->subtitle }}</p>

            <a href="{{ route('services.show', $svc->slug) }}"
               class="inline-flex items-center gap-2 text-pm-cyan text-sm font-bold
                      group-hover:text-pm-gold transition-colors duration-300">
              {{ $servicesIntroContent['card_link_label'] }}
              <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
              </svg>
            </a>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     PORTFOLIO PREVIEW
════════════════════════════════════════════════════════════════ --}}
<section class="section-padding bg-pm-navy relative overflow-hidden">
  <div class="absolute inset-0 grid-overlay pointer-events-none"></div>
  <div class="absolute top-0 right-0 w-96 h-96 bg-pm-cyan/5 rounded-full blur-3xl pointer-events-none"></div>

  <div class="container-custom relative z-10">
    @php
      $portfolioIntroContent = $homepageContent->portfolioIntro($homeSections->get('portfolio_intro'));
      $portfolioCards = $homepageContent->portfolioCards($portfolios ?? collect());
    @endphp

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16">
      <div class="aos">
        <p class="text-pm-gold text-[10px] font-bold uppercase tracking-[0.3em] mb-3">{{ $portfolioIntroContent['eyebrow'] }}</p>
        <h2 class="section-title-white">{{ $portfolioIntroContent['title'] }}</h2>
        <p class="text-gray-400 text-lg mt-4 max-w-xl">
          {{ $portfolioIntroContent['subtitle'] }}
        </p>
      </div>
      <a href="{{ $portfolioIntroContent['cta_url'] }}" class="btn-secondary text-sm self-start shrink-0">
        {{ $portfolioIntroContent['cta_label'] }}
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
      </a>
    </div>


    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      @foreach($portfolioCards as $project)
        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden
                    hover:bg-white/10 hover:border-white/20 transition-all duration-300 group aos">
          {{-- Image placeholder --}}
          <div class="h-48 bg-gradient-to-br from-pm-navy to-[#102040] relative flex items-center justify-center border-b border-white/10">
            <svg class="w-16 h-16 text-pm-cyan/15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <div class="absolute top-3 left-3">
              <span class="badge bg-pm-cyan/20 text-pm-cyan border border-pm-cyan/30 text-[10px]">
                {{ $project['category_label'] }}
              </span>
            </div>
          </div>

          <div class="p-6">
            <h3 class="text-white font-bold font-heading mb-2 group-hover:text-pm-cyan
                       transition-colors text-lg">{{ $project['title'] }}</h3>
            <p class="text-gray-400 text-sm leading-relaxed mb-4">{{ $project['description'] }}</p>

            {{-- Result badge --}}
            <div class="bg-green-500/10 border border-green-500/20 rounded-lg px-3 py-2 mb-4">
              <p class="text-green-400 text-xs font-bold">📈 {{ $project['result'] }}</p>
            </div>

            {{-- Tech tags --}}
            <div class="flex flex-wrap gap-1.5 mb-5">
              @foreach($project['technologies'] as $t)
                <span class="text-[10px] bg-white/5 text-gray-400 border border-white/10
                             px-2 py-0.5 rounded-md">{{ $t }}</span>
              @endforeach
            </div>

            <a href="{{ $project['url'] }}"
               class="text-pm-cyan text-sm font-bold hover:text-pm-gold transition-colors
                      flex items-center gap-1.5 group/link">
              {{ $portfolioIntroContent['card_link_label'] }}
              <svg class="w-4 h-4 group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
              </svg>
            </a>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     LATEST ARTICLES
════════════════════════════════════════════════════════════════ --}}
<section class="section-padding bg-pm-grey">
  <div class="container-custom">
    @php
      $blogIntroContent = $homepageContent->blogIntro($homeSections->get('blog_intro'));
      $blogCards = $homepageContent->blogCards($articles ?? collect());
    @endphp

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16">
      <div class="aos">
        <p class="section-label mb-3">{{ $blogIntroContent['eyebrow'] }}</p>
        <h2 class="section-title">{{ $blogIntroContent['title'] }}</h2>
        <p class="text-gray-500 text-lg mt-4 max-w-xl">
          {{ $blogIntroContent['subtitle'] }}
        </p>
      </div>
      <a href="{{ $blogIntroContent['cta_url'] }}" class="btn-outline text-sm self-start shrink-0">
        {{ $blogIntroContent['cta_label'] }}
      </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      @foreach($blogCards as $article)
        <article class="card-hover aos group">
          {{-- Featured image placeholder --}}
          <div class="h-44 rounded-xl mb-5 overflow-hidden bg-gradient-to-br from-pm-cyan/8 to-pm-navy/10 flex items-center justify-center">
            <svg class="w-12 h-12 text-pm-cyan/25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>

          <div class="flex items-center gap-3 mb-3">
            <span class="badge-cyan text-[10px]">{{ $article['category_label'] }}</span>
            <span class="text-xs text-gray-400">{{ $article['date'] }}</span>
          </div>

          <h3 class="text-pm-navy font-extrabold font-heading text-base leading-snug mb-3
                     group-hover:text-pm-cyan transition-colors">{{ $article['title'] }}</h3>
          <p class="text-gray-500 text-sm leading-relaxed mb-5 line-clamp-3">{{ $article['excerpt'] }}</p>

          <a href="{{ $article['url'] }}"
             class="inline-flex items-center gap-1.5 text-pm-cyan text-sm font-bold
                    hover:text-pm-navy transition-colors">
            {{ $blogIntroContent['card_link_label'] }}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
          </a>
        </article>
      @endforeach
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     FINAL CTA
════════════════════════════════════════════════════════════════ --}}
<section class="bg-cta-gradient relative overflow-hidden py-28">
  <div class="absolute inset-0 dot-overlay pointer-events-none opacity-50"></div>
  <div class="absolute -top-32 -left-32 w-96 h-96 bg-pm-cyan/10 rounded-full blur-3xl pointer-events-none"></div>
  <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-pm-gold/5 rounded-full blur-3xl pointer-events-none"></div>

  <div class="container-custom relative z-10 text-center">
    @php
      $primaryCtaContent = $homepageContent->primaryCta($homeSections->get('primary_cta'));
    @endphp

    <span class="badge bg-pm-gold/10 text-pm-gold border border-pm-gold/20 mb-6">{{ $primaryCtaContent['eyebrow'] }}</span>

    <h2 class="text-4xl md:text-5xl xl:text-6xl font-extrabold text-white font-heading mt-6 mb-6 leading-tight">
      {{ $primaryCtaContent['title_lines'][0] }}
      <br>
      <span class="text-gradient">{{ $primaryCtaContent['title_lines'][1] }}</span>
    </h2>

    <p class="text-gray-400 text-xl mb-12 max-w-2xl mx-auto">
      {{ $primaryCtaContent['subtitle'] }}
    </p>

    <div class="flex flex-wrap items-center justify-center gap-4 mb-14">
      <a href="{{ $primaryCtaContent['primary_url'] }}" class="btn-gold text-base px-10 py-5 font-extrabold">
        {{ $primaryCtaContent['primary_label'] }}
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
      </a>
      <a href="{{ $primaryCtaContent['secondary_url'] }}" class="btn-secondary text-base px-10 py-5">
        {{ $primaryCtaContent['secondary_label'] }}
      </a>
    </div>

    {{-- Social proof chips --}}
    <div class="flex flex-wrap items-center justify-center gap-6 text-gray-500 text-sm">
      @foreach($primaryCtaContent['proof_points'] as $proof)
        <span class="flex items-center gap-2">
          <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
          </svg>
          {{ $proof }}
        </span>
      @endforeach
    </div>
  </div>
</section>

@endsection
