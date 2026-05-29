@extends('layouts.public')

@section('meta_title',       'Portfolio — Prosper Media Projects & Case Studies')
@section('meta_description', 'Real results from Google Ads campaigns, conversion tracking setups and web development projects.')

@section('content')

{{-- Hero --}}
<section class="bg-hero-gradient py-24 relative overflow-hidden">
  <div class="absolute inset-0 grid-overlay pointer-events-none"></div>
  <div class="container-custom relative z-10 text-center max-w-3xl mx-auto">
    <p class="section-label mb-4">Our Work</p>
    <h1 class="text-4xl md:text-5xl xl:text-6xl font-extrabold text-white font-heading leading-tight mb-6">
      Real Results from
      <span class="text-gradient"> Real Campaigns</span>
    </h1>
    <p class="text-gray-300 text-xl">
      Every project is a case study in technical precision.
    </p>
  </div>
</section>

{{-- Stats --}}
<section class="bg-white border-b border-gray-100 py-10">
  <div class="container-custom">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
      @foreach([['150','+','Projects'],['4.8','x','Avg ROAS'],['98','%','Satisfaction'],['85','%','Data Recovery']] as [$n,$s,$l])
        <div>
          <p class="text-3xl font-extrabold text-pm-navy font-heading"
             data-counter="{{ $n }}" data-suffix="{{ $s }}">{{ $n }}{{ $s }}</p>
          <p class="text-gray-500 text-sm mt-1">{{ $l }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- Portfolio Grid --}}
<section class="section-padding bg-pm-grey"
         x-data="{ activeFilter: '{{ $category ?? 'all' }}' }">
  <div class="container-custom">

    {{-- Filters --}}
    <div class="flex flex-wrap items-center justify-center gap-3 mb-12">
      @php
        $filters = ['all'=>'All Projects','google_ads'=>'Google Ads','tracking_setup'=>'Tracking Setup',
                    'web_development'=>'Web Development','landing_page'=>'Landing Page','automation'=>'Automation'];
      @endphp
      @foreach($filters as $key => $label)
        <button @click="activeFilter = '{{ $key }}'"
                :class="activeFilter === '{{ $key }}'
                  ? 'bg-pm-navy text-white border-pm-navy'
                  : 'bg-white text-pm-slate border-gray-200 hover:border-pm-cyan hover:text-pm-cyan'"
                class="px-5 py-2.5 rounded-xl text-sm font-semibold border transition-all duration-200">
          {{ $label }}
        </button>
      @endforeach
    </div>

    {{-- ── Use DB data if available, else static fallback ── --}}
    @php
      $categoryColors = [
        'google_ads'      => '#4285F4',
        'tracking_setup'  => '#F57C00',
        'web_development' => '#FF2D20',
        'landing_page'    => '#00B4D8',
        'automation'      => '#FFB703',
      ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

      {{-- DB Projects --}}
      @if(isset($portfolioProjects) && $portfolioProjects->count() > 0)
        @foreach($portfolioProjects as $project)
          <div x-show="activeFilter === 'all' || activeFilter === '{{ $project->category }}'"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0 scale-95"
               x-transition:enter-end="opacity-100 scale-100"
               class="card group hover:-translate-y-1 transition-all duration-300">

            @php $catColor = $categoryColors[$project->category] ?? '#00B4D8'; @endphp

            <div class="h-44 rounded-xl mb-5 flex items-center justify-center relative overflow-hidden"
                 style="background: linear-gradient(135deg, {{ $catColor }}15, {{ $catColor }}05);">
              @if($project->featured_image)
                <img src="{{ Storage::url($project->featured_image) }}"
                     alt="{{ $project->title }}"
                     class="w-full h-full object-cover rounded-xl">
              @else
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center"
                     style="background-color: {{ $catColor }}20;">
                  <svg class="w-8 h-8" style="color: {{ $catColor }}"
                       fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                </div>
              @endif
              <div class="absolute top-3 left-3">
                <span class="badge text-[10px] text-white"
                      style="background-color: {{ $catColor }}cc; border-color: transparent;">
                  {{ $project->category_label }}
                </span>
              </div>
              @if($project->is_featured)
                <div class="absolute top-3 right-3">
                  <span class="badge bg-pm-gold/90 text-pm-navy border-transparent text-[10px] font-bold">
                    ⭐ Featured
                  </span>
                </div>
              @endif
            </div>

            <h3 class="text-pm-navy font-extrabold font-heading mb-2 group-hover:text-pm-cyan transition-colors">
              {{ $project->title }}
            </h3>
            <p class="text-gray-500 text-sm leading-relaxed mb-4">
              {{ Str::limit($project->short_description, 120) }}
            </p>

            @if($project->result_summary)
              <div class="bg-green-50 border border-green-100 rounded-xl px-4 py-2.5 mb-4">
                <p class="text-green-700 text-xs font-bold">📈 {{ $project->result_summary }}</p>
              </div>
            @endif

            @if($project->technologies)
              <div class="flex flex-wrap gap-1.5 mb-5">
                @foreach($project->technologies as $tech)
                  <span class="text-[10px] bg-pm-grey text-pm-slate border border-gray-200 px-2.5 py-1 rounded-lg font-medium">
                    {{ $tech }}
                  </span>
                @endforeach
              </div>
            @endif

            <a href="{{ route('portfolio.show', $project->slug) }}"
               class="inline-flex items-center gap-2 text-pm-cyan text-sm font-bold hover:text-pm-navy transition-colors">
              View Case Study
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
              </svg>
            </a>
          </div>
        @endforeach

      @else
        {{-- Static fallback --}}
        @foreach([
          ['E-Commerce Google Ads Full Funnel Rebuild',  'google_ads',       'Complete funnel rebuild with Smart Bidding and audience segmentation.',        '4.2x ROAS in 90 days',       ['Google Ads','GTM','GA4']],
          ['GTM Server-Side + Meta CAPI Setup',           'tracking_setup',   'Server-side GTM container with Facebook Conversion API.',                      '85% conversion data recovered',['GTM Server','Meta CAPI','Cloud Run']],
          ['Laravel SaaS Landing Page',                   'web_development',  'High-converting SaaS landing page with A/B testing and speed optimisation.',   'CTR 4.2% → 11.8%',           ['Laravel','Tailwind','MySQL']],
          ['Legal Services Lead Gen Page',                'landing_page',     'Redesigned landing page with trust signals and form optimisation.',             'CPL reduced by 80%',         ['HTML5','Tailwind','GTM']],
          ['Local Business Performance Max Campaigns',    'google_ads',       'Local search + PMax with call tracking and location extensions.',               'Call volume tripled',         ['Google Ads','Call Tracking','GA4']],
          ['AI Lead Qualification Pipeline',              'automation',       'Automated lead scoring via OpenAI + CRM population triggered by form events.', '14 hours/week saved',        ['Laravel','OpenAI','Make.com']],
        ] as [$title, $cat, $desc, $result, $tech])
          @php $catColor = $categoryColors[$cat] ?? '#00B4D8'; @endphp
          <div x-show="activeFilter === 'all' || activeFilter === '{{ $cat }}'"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0 scale-95"
               x-transition:enter-end="opacity-100 scale-100"
               class="card group hover:-translate-y-1 transition-all duration-300">

            <div class="h-44 rounded-xl mb-5 flex items-center justify-center"
                 style="background: linear-gradient(135deg, {{ $catColor }}15, {{ $catColor }}05);">
              <div class="w-14 h-14 rounded-2xl flex items-center justify-center"
                   style="background-color: {{ $catColor }}20;">
                <svg class="w-7 h-7" style="color: {{ $catColor }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
              </div>
            </div>

            <h3 class="text-pm-navy font-extrabold font-heading mb-2 group-hover:text-pm-cyan transition-colors">{{ $title }}</h3>
            <p class="text-gray-500 text-sm leading-relaxed mb-4">{{ $desc }}</p>
            <div class="bg-green-50 border border-green-100 rounded-xl px-4 py-2.5 mb-4">
              <p class="text-green-700 text-xs font-bold">📈 {{ $result }}</p>
            </div>
            <div class="flex flex-wrap gap-1.5 mb-5">
              @foreach($tech as $t)
                <span class="text-[10px] bg-pm-grey text-pm-slate border border-gray-200 px-2.5 py-1 rounded-lg font-medium">{{ $t }}</span>
              @endforeach
            </div>
            <a href="{{ route('contact') }}"
               class="inline-flex items-center gap-2 text-pm-cyan text-sm font-bold hover:text-pm-navy transition-colors">
              Discuss This Project
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
              </svg>
            </a>
          </div>
        @endforeach
      @endif

    </div>
  </div>
</section>

{{-- CTA --}}
<section class="bg-cta-gradient py-20 relative overflow-hidden">
  <div class="absolute inset-0 dot-overlay pointer-events-none opacity-40"></div>
  <div class="container-custom relative z-10 text-center">
    <h2 class="text-3xl md:text-4xl font-extrabold text-white font-heading mb-4">Want Results Like These?</h2>
    <p class="text-gray-400 text-lg mb-8 max-w-xl mx-auto">Let's discuss your project and build a measurable growth strategy.</p>
    <a href="{{ route('contact') }}" class="btn-gold text-base px-10 py-4">Start Your Project</a>
  </div>
</section>

@endsection

{{-- Below is the old services single page code for reference when building out portfolio single pages --}}

{{-- Service details --}}