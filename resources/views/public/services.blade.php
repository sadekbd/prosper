@extends('layouts.public')

@section('meta_title',       'Our Services — Google Ads, Conversion Tracking & Web Development')
@section('meta_description', 'Expert Google Ads management, advanced conversion tracking setup, and professional Laravel web development engineered for measurable ROI.')

@section('content')

{{-- ── Hero ─────────────────────────────────────────────────────── --}}
<section class="bg-hero-gradient py-24 relative overflow-hidden">
  <div class="absolute inset-0 grid-overlay pointer-events-none"></div>
  <div class="container-custom relative z-10 text-center max-w-3xl mx-auto">
    <p class="section-label mb-4">What We Do</p>
    <h1 class="text-4xl md:text-5xl xl:text-6xl font-extrabold text-white font-heading leading-tight mb-6">
      Services Built for
      <span class="text-gradient"> Measurable Results</span>
    </h1>
    <p class="text-gray-300 text-xl leading-relaxed">
      Three core service pillars — each engineered with technical precision to maximise
      your return on digital investment.
    </p>
  </div>
</section>

{{-- ── Service Pillars ─────────────────────────────────────────── --}}
@php
  // Static fallback data
  $staticServices = collect([
    (object)[
      'id'       => 1,
      'title'    => 'Google Ads Mastery',
      'subtitle' => 'Maximise your ROI with data-driven search advertising.',
      'description' => 'We do not just run ads. We engineer profitable campaigns through high-intent keyword research, strategic bidding, compelling ad copy, landing page alignment, and ongoing optimisation.',
      'slug'     => 'google-ads-mastery',
      'color'    => 'cyan',
      'icon_path'=> 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
      'feats'    => ['Search & Display Campaigns','Competitor Analysis & Keyword Strategy','A/B Testing & Ad Optimisation','Budget Management & Forecasting','Remarketing Campaigns','Monthly Performance Reporting'],
    ],
    (object)[
      'id'       => 2,
      'title'    => 'Advanced Conversion Tracking',
      'subtitle' => 'Stop guessing and start measuring.',
      'description' => 'We bridge the gap between your website and your marketing data using professional-grade tracking setup that captures every touchpoint, micro-conversion, and lead attribution path.',
      'slug'     => 'advanced-conversion-tracking',
      'color'    => 'gold',
      'icon_path'=> 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z',
      'feats'    => ['Google Tag Manager Setup','Meta Pixel & CAPI Setup','Server-Side Tracking','API Conversion Tracking','Custom Events & Goals','Lead Attribution & ROI Reporting'],
    ],
    (object)[
      'id'       => 3,
      'title'    => 'Professional Web Development',
      'subtitle' => 'High-performance websites built for conversion.',
      'description' => 'A beautiful website is useless if it does not convert. We build fast, secure, responsive, and conversion-focused websites using Laravel, PHP, MySQL and modern front-end technologies.',
      'slug'     => 'professional-web-development',
      'color'    => 'cyan',
      'icon_path'=> 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
      'feats'    => ['Custom Web Development','Landing Page Optimisation','Database Integration','Secure Backend Logic','Speed & Core Web Vitals Optimisation','Mobile-First Design'],
    ],
  ]);

  // Use DB services if available, otherwise static
  $displayServices = (isset($services) && $services->count() > 0) ? $services : $staticServices;

  $iconPaths = [
    'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z',
    'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
  ];
  $colors = ['cyan','gold','cyan'];
@endphp

@foreach($displayServices as $i => $svc)

  {{-- ── Normalize features to plain string array — KEY FIX ── --}}
  @php
    if (isset($svc->features) && $svc->features instanceof \Illuminate\Support\Collection) {
      // Eloquent Collection of ServiceFeature models → extract strings
      $featureList = $svc->features->pluck('feature')->toArray();
    } elseif (isset($svc->feats) && is_array($svc->feats)) {
      // Static stdClass with feats array of strings
      $featureList = $svc->feats;
    } else {
      $featureList = [];
    }

    // Per-service icon and color (use DB values if present, else cycle)
    $iconPath  = $svc->icon_path  ?? ($iconPaths[$i]  ?? $iconPaths[0]);
    $cardColor = $svc->color      ?? ($colors[$i]     ?? 'cyan');
    $isGold    = $cardColor === 'gold';

    $isCyanText  = $isGold ? 'text-pm-gold'  : 'text-pm-cyan';
    $isCyanBg    = $isGold ? 'bg-pm-gold/10' : 'bg-pm-cyan/10';
    $isCyanBg2   = $isGold ? 'bg-pm-gold/20' : 'bg-pm-cyan/20';
    $isCyanCheck = $isGold ? 'bg-pm-gold/15' : 'bg-pm-cyan/15';
  @endphp

  <section class="section-padding {{ $i % 2 === 0 ? 'bg-white' : 'bg-pm-grey' }}">
    <div class="container-custom">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

        {{-- ── Left: Text Content ── --}}
        <div class="{{ $i % 2 !== 0 ? 'lg:order-2' : '' }} aos">
          <span class="text-[10px] font-black uppercase tracking-widest {{ $isCyanText }} mb-3 block">
            Service {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
          </span>
          <h2 class="text-3xl md:text-4xl font-extrabold text-pm-navy font-heading mb-3">
            {{ $svc->title }}
          </h2>
          <p class="{{ $isCyanText }} font-semibold text-lg mb-5">
            {{ $svc->subtitle }}
          </p>
          <p class="text-gray-500 leading-relaxed mb-8">
            {{ $svc->description }}
          </p>

          {{-- Feature checklist --}}
          <ul class="space-y-3 mb-8">
            @foreach($featureList as $feat)
              <li class="flex items-center gap-3">
                <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 {{ $isCyanCheck }}">
                  <svg class="w-3 h-3 {{ $isCyanText }}" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                          clip-rule="evenodd"/>
                  </svg>
                </div>
                {{-- $feat is always a plain string now --}}
                <span class="text-pm-slate text-sm font-medium">{{ $feat }}</span>
              </li>
            @endforeach
          </ul>

          <a href="{{ route('contact') }}" class="btn-primary">
            Get Started with This Service
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
          </a>
        </div>

        {{-- ── Right: Visual Card ── --}}
        <div class="{{ $i % 2 !== 0 ? 'lg:order-1' : '' }} aos">
          <div class="bg-pm-navy rounded-2xl p-8 glow-cyan relative overflow-hidden">
            <div class="absolute inset-0 grid-overlay pointer-events-none rounded-2xl opacity-50"></div>

            <div class="relative z-10">
              {{-- Icon --}}
              <div class="w-16 h-16 {{ $isCyanBg }} rounded-2xl flex items-center justify-center mb-6">
                <svg class="w-8 h-8 {{ $isCyanText }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"/>
                </svg>
              </div>

              <h3 class="text-white font-extrabold font-heading text-2xl mb-2">{{ $svc->title }}</h3>
              <p class="{{ $isCyanText }} font-medium mb-6 text-sm">{{ $svc->subtitle }}</p>

              {{-- Mini checklist preview (first 3 features) --}}
              @foreach(array_slice($featureList, 0, 3) as $feat)
                <div class="flex items-center gap-3 mb-3">
                  <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                          clip-rule="evenodd"/>
                  </svg>
                  {{-- $feat is always a plain string --}}
                  <span class="text-gray-300 text-sm">{{ $feat }}</span>
                </div>
              @endforeach

              @if(count($featureList) > 3)
                <p class="text-gray-500 text-xs mt-2 ml-7">
                  + {{ count($featureList) - 3 }} more included
                </p>
              @endif

              <div class="mt-6 pt-6 border-t border-white/10 flex items-center justify-between">
                <span class="text-gray-400 text-xs">Free audit included</span>
                <a href="{{ route('contact') }}"
                   class="text-xs {{ $isCyanText }} font-bold hover:underline transition-colors">
                  Let's Talk →
                </a>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

@endforeach

{{-- ── Process Section ─────────────────────────────────────────── --}}
<section class="section-padding bg-pm-navy relative overflow-hidden">
  <div class="absolute inset-0 grid-overlay pointer-events-none"></div>
  <div class="container-custom relative z-10">

    <div class="text-center mb-16 aos">
      <p class="text-pm-gold text-[10px] font-black uppercase tracking-[0.3em] mb-3">How We Work</p>
      <h2 class="section-title-white">Our Process</h2>
      <p class="text-gray-400 text-lg mt-4 max-w-2xl mx-auto">
        A clear, structured approach that eliminates guesswork and delivers consistent results.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      @foreach([
        ['01', 'Free Audit',      'We analyse your current setup — ads, tracking, website — and identify every gap and opportunity.'],
        ['02', 'Strategy Build',  'We build a data-driven strategy tailored to your business goals, budget, and market position.'],
        ['03', 'Implementation',  'We execute with precision — tracking setup, campaign launch, or website build — done properly.'],
        ['04', 'Optimise & Grow', 'Continuous monitoring, testing, and optimisation to compound results over time.'],
      ] as [$num, $step, $desc])
        <div class="relative aos">
          {{-- Connector line --}}
          @if($loop->index < 3)
            <div class="hidden md:block absolute top-8 left-1/2 w-full h-px bg-white/10 z-0"></div>
          @endif

          <div class="relative z-10 text-center">
            <div class="w-16 h-16 rounded-full bg-pm-cyan/10 border-2 border-pm-cyan/30
                        flex items-center justify-center mx-auto mb-5">
              <span class="text-pm-cyan font-extrabold font-heading text-lg">{{ $num }}</span>
            </div>
            <h3 class="text-white font-extrabold font-heading mb-3">{{ $step }}</h3>
            <p class="text-gray-400 text-sm leading-relaxed">{{ $desc }}</p>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ── Final CTA ────────────────────────────────────────────────── --}}
<section class="bg-cta-gradient py-20 relative overflow-hidden">
  <div class="absolute inset-0 dot-overlay pointer-events-none opacity-40"></div>
  <div class="container-custom relative z-10 text-center">
    <h2 class="text-3xl md:text-4xl font-extrabold text-white font-heading mb-4">
      Not Sure Which Service You Need?
    </h2>
    <p class="text-gray-400 text-lg mb-8 max-w-xl mx-auto">
      Book a free technical audit and we'll tell you exactly what will move the needle.
    </p>
    <a href="{{ route('contact') }}" class="btn-gold text-base px-10 py-4">
      Book My Free Audit
    </a>
  </div>
</section>

@endsection