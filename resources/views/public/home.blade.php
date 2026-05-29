@extends('layouts.public')

@section('meta_title',       'Prosper Media — Engineering Digital Success with Technical Precision')
@section('meta_description', 'Tech-first Google Ads management, conversion tracking, AI automation and high-performance web development. ROI-focused digital growth.')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════
     HERO
════════════════════════════════════════════════════════════════ --}}
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
          Be Optimistic
        </div>

        <h1 class="text-4xl md:text-5xl xl:text-6xl font-extrabold text-white leading-[1.1] mb-6 font-heading">
          Engineering Digital
          <br>
          <span class="text-gradient">Success</span> with
          <br>
          Technical Precision.
        </h1>

        <p class="text-gray-300 text-lg md:text-xl leading-relaxed mb-10 max-w-lg">
          Professional Google Ads management, conversion tracking, and high-performance
          web development built to turn visitors into loyal customers.
        </p>

        <div class="flex flex-wrap gap-4 mb-12">
          <a href="{{ route('services') }}" class="btn-primary px-8 py-4 text-base">
            View Our Services
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
          </a>
          <a href="{{ route('contact') }}" class="btn-secondary px-8 py-4 text-base">
            Get a Free Audit
          </a>
        </div>

        {{-- Stats --}}
        <div class="flex flex-wrap gap-6 pt-8 border-t border-white/10">
          @foreach([
            ['150','+',' ', 'Projects Done'],
            ['98', '%', '', 'Client Satisfaction'],
            ['5',  'x', '', 'Average ROAS'],
            ['3',  '+', 'yr','Experience'],
          ] as [$num, $sfx, $sep, $label])
            <div>
              <p class="text-2xl font-extrabold text-pm-cyan font-heading"
                 data-counter="{{ $num }}" data-suffix="{{ $sfx }}">{{ $num }}{{ $sfx }}</p>
              <p class="text-xs text-gray-400 mt-0.5">{{ $label }}</p>
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
              <p class="text-[10px] text-gray-500 uppercase tracking-widest">Live Dashboard</p>
              <p class="text-white font-bold font-heading">Q2 Campaign Performance</p>
            </div>
            <span class="flex items-center gap-1.5 text-xs text-green-400 font-medium">
              <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
              Live
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
                <p class="text-[10px] text-gray-500 uppercase mb-1">{{ $kpi }}</p>
                <p class="text-lg font-bold font-heading {{ $col }}">{{ $val }}</p>
                <p class="text-[10px] text-green-400 mt-0.5">{{ $chg }}</p>
              </div>
            @endforeach
          </div>

          {{-- Bar chart --}}
          <div class="mb-4">
            <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-3">Weekly Conversions</p>
            <div class="flex items-end gap-1.5 h-16">
              @foreach([35, 55, 42, 70, 60, 85, 75] as $h)
                <div class="flex-1 rounded-t bg-pm-cyan/25 hover:bg-pm-cyan/50 transition-colors"
                     style="height:{{ $h }}%"></div>
              @endforeach
            </div>
            <div class="flex justify-between mt-1.5">
              @foreach(['Mo','Tu','We','Th','Fr','Sa','Su'] as $d)
                <span class="text-[9px] text-gray-600 flex-1 text-center">{{ $d }}</span>
              @endforeach
            </div>
          </div>

          {{-- Tracking tags --}}
          <div class="flex flex-wrap items-center gap-2 pt-4 border-t border-white/5">
            <p class="text-[10px] text-gray-500 mr-1">Tracking:</p>
            @foreach(['GTM', 'GA4', 'Meta API', 'Server-Side'] as $tag)
              <span class="text-[10px] bg-pm-cyan/10 text-pm-cyan px-2 py-0.5 rounded-md font-medium">{{ $tag }}</span>
            @endforeach
          </div>
        </div>

        {{-- Floating badges --}}
        <div class="absolute -top-4 -right-4 bg-pm-gold text-pm-navy rounded-xl px-4 py-2
                    text-sm font-extrabold shadow-xl font-heading shadow-yellow-500/20">
          ROI ↑ 340%
        </div>
        <div class="absolute -bottom-4 -left-4 bg-white text-pm-navy rounded-xl px-4 py-2
                    text-xs font-semibold shadow-xl flex items-center gap-2">
          <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
          Tracking Active
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
<section class="bg-white border-y border-gray-100 py-12">
  <div class="container-custom">
    <p class="text-center text-[10px] text-gray-400 uppercase tracking-[0.3em] font-semibold mb-8">
      Technologies & Platforms We Master
    </p>
    <div class="flex flex-wrap items-center justify-center gap-6 md:gap-12">
      @foreach([
        ['Google Ads',    '#4285F4'],
        ['Tag Manager',   '#F57C00'],
        ['Meta Pixel',    '#1877F2'],
        ['Laravel',       '#FF2D20'],
        ['MySQL',         '#4479A1'],
        ['Analytics GA4', '#E37400'],
        ['PHP 8',         '#777BB4'],
      ] as [$tool, $color])
        <div class="flex items-center gap-2 text-gray-400 hover:text-pm-slate
                    transition-all duration-200 group cursor-default">
          <div class="w-2.5 h-2.5 rounded-full group-hover:scale-125 transition-transform"
               style="background-color: {{ $color }}"></div>
          <span class="text-sm font-semibold tracking-wide">{{ $tool }}</span>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     WHY CHOOSE US
════════════════════════════════════════════════════════════════ --}}
<section class="section-padding bg-pm-grey">
  <div class="container-custom">

    <div class="text-center mb-16 aos">
      <p class="section-label mb-3">Why Choose Us</p>
      <h2 class="section-title">The Prosper Media Difference</h2>
      <p class="text-gray-500 text-lg mt-4 max-w-2xl mx-auto">
        We combine deep technical expertise with marketing intelligence
        to deliver results others simply can't match.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      @php
        $pillars = [
          [
            'color'  => 'cyan',
            'icon'   => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
            'title'  => 'Tech-First Marketing',
            'desc'   => 'We code the tracking setup that other agencies miss. Every pixel, every event, every conversion — captured with precision using GTM, server-side tracking, and custom API integrations.',
            'badge'  => 'No. 01',
          ],
          [
            'color'  => 'gold',
            'icon'   => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
            'title'  => 'ROI Focused',
            'desc'   => 'Every click is treated as an investment, not an expense. We obsess over ROAS, CPA, and conversion rates — building campaigns that compound in profitability over time.',
            'badge'  => 'No. 02',
          ],
          [
            'color'  => 'cyan',
            'icon'   => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
            'title'  => 'Transparent Data',
            'desc'   => 'Clear reporting, measurable results, and honest technical support. You always know exactly what is happening with your campaigns and why it\'s working.',
            'badge'  => 'No. 03',
          ],
        ];
      @endphp

      @foreach($pillars as $p)
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
<section class="section-padding bg-white">
  <div class="container-custom">

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16">
      <div class="aos">
        <p class="section-label mb-3">What We Do</p>
        <h2 class="section-title">Our Core Services</h2>
        <p class="text-gray-500 text-lg mt-4 max-w-xl">
          Three pillars of technical excellence powering your entire digital growth engine.
        </p>
      </div>
      <a href="{{ route('services') }}" class="btn-outline text-sm self-start shrink-0">
        View All Services
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
      </a>
    </div>

    @php
      $coreServices = isset($services) && $services->count() > 0
        ? $services->take(3)
        : collect([
            (object)['title'=>'Google Ads Mastery',          'subtitle'=>'Maximise your ROI with data-driven search advertising.','slug'=>'google-ads-mastery'],
            (object)['title'=>'Advanced Conversion Tracking','subtitle'=>'Stop guessing and start measuring every touchpoint.',    'slug'=>'advanced-conversion-tracking'],
            (object)['title'=>'Professional Web Development','subtitle'=>'High-performance websites built for conversion.',         'slug'=>'professional-web-development'],
          ]);
      $icons = [
        'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z',
        'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
      ];
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$i] }}"/>
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
              Learn More
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

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16">
      <div class="aos">
        <p class="text-pm-gold text-[10px] font-bold uppercase tracking-[0.3em] mb-3">Our Work</p>
        <h2 class="section-title-white">Recent Projects</h2>
        <p class="text-gray-400 text-lg mt-4 max-w-xl">
          Real results from real campaigns. See how we engineer digital success.
        </p>
      </div>
      <a href="{{ route('portfolio') }}" class="btn-secondary text-sm self-start shrink-0">
        All Projects
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
      </a>
    </div>

    @php
      $demoPortfolio = [
        ['E-Commerce Google Ads Overhaul',  'google_ads',       'Full-funnel campaign with audience segmentation, dynamic remarketing, and Smart Bidding strategy.',   '320% ROAS — 3 Months',   ['Google Ads','GTM','GA4']],
        ['GTM + Meta CAPI Server Tracking', 'tracking_setup',   'Server-side Facebook Conversion API setup eliminating browser data loss from iOS 14+ changes.',       '85% Data Recovery',      ['Meta CAPI','GTM','Node.js']],
        ['Laravel SaaS Landing Page',        'web_development',  'High-converting, mobile-first landing page with A/B testing, heatmaps, and speed optimization.',      'CTR: 4.2% → 11.8%',     ['Laravel','MySQL','Tailwind']],
      ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      @foreach($demoPortfolio as [$title, $cat, $desc, $result, $tech])
        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden
                    hover:bg-white/10 hover:border-white/20 transition-all duration-300 group aos">
          {{-- Image placeholder --}}
          <div class="h-48 bg-gradient-to-br from-pm-navy to-[#102040] relative flex items-center justify-center border-b border-white/10">
            <svg class="w-16 h-16 text-pm-cyan/15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <div class="absolute top-3 left-3">
              <span class="badge bg-pm-cyan/20 text-pm-cyan border border-pm-cyan/30 text-[10px]">
                {{ ucfirst(str_replace('_', ' ', $cat)) }}
              </span>
            </div>
          </div>

          <div class="p-6">
            <h3 class="text-white font-bold font-heading mb-2 group-hover:text-pm-cyan
                       transition-colors text-lg">{{ $title }}</h3>
            <p class="text-gray-400 text-sm leading-relaxed mb-4">{{ $desc }}</p>

            {{-- Result badge --}}
            <div class="bg-green-500/10 border border-green-500/20 rounded-lg px-3 py-2 mb-4">
              <p class="text-green-400 text-xs font-bold">📈 {{ $result }}</p>
            </div>

            {{-- Tech tags --}}
            <div class="flex flex-wrap gap-1.5 mb-5">
              @foreach($tech as $t)
                <span class="text-[10px] bg-white/5 text-gray-400 border border-white/10
                             px-2 py-0.5 rounded-md">{{ $t }}</span>
              @endforeach
            </div>

            <a href="{{ route('portfolio') }}"
               class="text-pm-cyan text-sm font-bold hover:text-pm-gold transition-colors
                      flex items-center gap-1.5 group/link">
              View Case Study
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

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16">
      <div class="aos">
        <p class="section-label mb-3">Knowledge Hub</p>
        <h2 class="section-title">Latest Articles</h2>
        <p class="text-gray-500 text-lg mt-4 max-w-xl">
          Practical insights, tutorials and guides from our technical team.
        </p>
      </div>
      <a href="{{ route('blog') }}" class="btn-outline text-sm self-start shrink-0">
        All Articles
      </a>
    </div>

    @php
      $demoArticles = [
        ['Google Ads Tips',   'How to Set Up Server-Side Conversion Tracking in 2025',          'A complete guide covering GTM server containers, transport URL setup, and debugging server-side tags for maximum conversion data accuracy.',  'May 15, 2025'],
        ['Tracking Guides',   'Server-Side vs Client-Side Tracking: The Complete Comparison',   'Understanding the technical difference between client-side pixels and server-side tracking and when each approach maximises your data accuracy.',  'May 8, 2025'],
        ['Web Dev Tutorials', 'Building High-Converting Laravel Landing Pages That Actually Convert', 'The technical and psychological framework behind landing pages that convert at 3x the industry average using Laravel and modern front-end techniques.',  'Apr 28, 2025'],
      ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      @foreach($demoArticles as [$cat, $title, $excerpt, $date])
        <article class="card-hover aos group">
          {{-- Featured image placeholder --}}
          <div class="h-44 rounded-xl mb-5 overflow-hidden bg-gradient-to-br from-pm-cyan/8 to-pm-navy/10 flex items-center justify-center">
            <svg class="w-12 h-12 text-pm-cyan/25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>

          <div class="flex items-center gap-3 mb-3">
            <span class="badge-cyan text-[10px]">{{ $cat }}</span>
            <span class="text-xs text-gray-400">{{ $date }}</span>
          </div>

          <h3 class="text-pm-navy font-extrabold font-heading text-base leading-snug mb-3
                     group-hover:text-pm-cyan transition-colors">{{ $title }}</h3>
          <p class="text-gray-500 text-sm leading-relaxed mb-5 line-clamp-3">{{ $excerpt }}</p>

          <a href="{{ route('blog') }}"
             class="inline-flex items-center gap-1.5 text-pm-cyan text-sm font-bold
                    hover:text-pm-navy transition-colors">
            Read Article
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
    <span class="badge bg-pm-gold/10 text-pm-gold border border-pm-gold/20 mb-6">Let's Work Together</span>

    <h2 class="text-4xl md:text-5xl xl:text-6xl font-extrabold text-white font-heading mt-6 mb-6 leading-tight">
      Ready to scale your business?
      <br>
      <span class="text-gradient">Be Optimistic.</span>
    </h2>

    <p class="text-gray-400 text-xl mb-12 max-w-2xl mx-auto">
      We've got the data covered. Let's engineer your digital success together
      with the precision your business deserves.
    </p>

    <div class="flex flex-wrap items-center justify-center gap-4 mb-14">
      <a href="{{ route('contact') }}" class="btn-gold text-base px-10 py-5 font-extrabold">
        Start Growing Today
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
      </a>
      <a href="{{ route('portfolio') }}" class="btn-secondary text-base px-10 py-5">
        See Our Work
      </a>
    </div>

    {{-- Social proof chips --}}
    <div class="flex flex-wrap items-center justify-center gap-6 text-gray-500 text-sm">
      @foreach(['No Long-Term Contracts', 'Free Audit Consultation', 'ROI-Focused Approach', '100% Transparent Reporting'] as $proof)
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