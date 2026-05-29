@extends('layouts.public')

@section('meta_title',       ($project->meta_title ?? $project->title . ' — Prosper Media Case Study'))
@section('meta_description', ($project->meta_description ?? $project->short_description))

@section('content')

{{-- ── Hero ─────────────────────────────────────────────────────── --}}
<section class="bg-hero-gradient py-20 relative overflow-hidden">
  <div class="absolute inset-0 grid-overlay pointer-events-none"></div>
  <div class="container-custom relative z-10">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-8 flex-wrap">
      <a href="{{ route('home') }}"      class="hover:text-white transition-colors">Home</a>
      <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
      </svg>
      <a href="{{ route('portfolio') }}" class="hover:text-white transition-colors">Portfolio</a>
      <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
      </svg>
      <span class="text-white truncate max-w-xs">{{ $project->title }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

      {{-- Text --}}
      <div>
        <div class="flex flex-wrap items-center gap-3 mb-6">
          <span class="badge bg-pm-cyan/20 text-pm-cyan border-pm-cyan/30">
            {{ $project->category_label }}
          </span>
          @if($project->is_featured)
            <span class="badge bg-pm-gold/20 text-pm-gold border-pm-gold/30">⭐ Featured Project</span>
          @endif
        </div>

        <h1 class="text-3xl md:text-4xl xl:text-5xl font-extrabold text-white font-heading leading-tight mb-5">
          {{ $project->title }}
        </h1>

        <p class="text-gray-300 text-lg leading-relaxed mb-8">
          {{ $project->short_description }}
        </p>

        {{-- Quick meta --}}
        <div class="flex flex-wrap gap-6">
          @if($project->client_name)
            <div>
              <p class="text-gray-500 text-xs uppercase tracking-widest mb-1">Client</p>
              <p class="text-white font-semibold text-sm">{{ $project->client_name }}</p>
            </div>
          @endif
          @if($project->result_summary)
            <div>
              <p class="text-gray-500 text-xs uppercase tracking-widest mb-1">Result</p>
              <p class="text-pm-gold font-bold text-sm">📈 {{ $project->result_summary }}</p>
            </div>
          @endif
          @if($project->project_url)
            <div>
              <p class="text-gray-500 text-xs uppercase tracking-widest mb-1">Live URL</p>
              <a href="{{ $project->project_url }}" target="_blank" rel="noopener noreferrer"
                 class="text-pm-cyan text-sm font-semibold hover:underline flex items-center gap-1">
                View Project
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
              </a>
            </div>
          @endif
        </div>
      </div>

      {{-- Result card --}}
      <div class="hidden lg:block">
        <div class="bg-white/5 border border-white/10 rounded-2xl p-8 backdrop-blur-sm">
          <p class="text-gray-400 text-xs uppercase tracking-widest mb-5">Project Overview</p>

          <div class="space-y-4">
            {{-- Technologies --}}
            @if($project->technologies && count($project->technologies) > 0)
              <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-2">Tech Stack</p>
                <div class="flex flex-wrap gap-2">
                  @foreach($project->technologies as $tech)
                    <span class="text-xs bg-pm-cyan/10 text-pm-cyan border border-pm-cyan/20
                                 px-3 py-1.5 rounded-lg font-medium">{{ $tech }}</span>
                  @endforeach
                </div>
              </div>
            @endif

            {{-- Category --}}
            <div class="pt-4 border-t border-white/10">
              <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Category</p>
              <p class="text-white font-semibold">{{ $project->category_label }}</p>
            </div>

            {{-- Result --}}
            @if($project->result_summary)
              <div class="pt-4 border-t border-white/10">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-2">Key Result</p>
                <div class="bg-green-500/10 border border-green-500/20 rounded-xl px-4 py-3">
                  <p class="text-green-400 font-bold">📈 {{ $project->result_summary }}</p>
                </div>
              </div>
            @endif
          </div>

          <a href="{{ route('contact') }}" class="btn-primary w-full justify-center text-sm mt-6">
            Get Similar Results
          </a>
        </div>
      </div>

    </div>
  </div>
</section>

{{-- ── Full Description ─────────────────────────────────────────── --}}
@if($project->full_description)
<section class="section-padding bg-white">
  <div class="container-custom">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-16">

      {{-- Main content --}}
      <div class="lg:col-span-2">
        <h2 class="text-2xl font-extrabold text-pm-navy font-heading mb-8">Case Study Details</h2>
        <div class="prose prose-slate max-w-none
                    prose-headings:font-heading prose-headings:text-pm-navy
                    prose-h2:text-xl prose-h3:text-lg
                    prose-a:text-pm-cyan prose-a:no-underline hover:prose-a:underline
                    prose-strong:text-pm-navy
                    prose-ul:space-y-1 prose-li:text-gray-600
                    prose-p:text-gray-600 prose-p:leading-relaxed">
          {!! $project->full_description !!}
        </div>
      </div>

      {{-- Sidebar --}}
      <div class="space-y-6">

        {{-- Technologies --}}
        @if($project->technologies && count($project->technologies) > 0)
          <div class="card">
            <h3 class="text-pm-navy font-extrabold font-heading mb-4 text-sm uppercase tracking-wider">
              Tech Stack Used
            </h3>
            <div class="flex flex-wrap gap-2">
              @foreach($project->technologies as $tech)
                <span class="text-xs bg-pm-grey text-pm-slate border border-gray-200
                             px-3 py-1.5 rounded-lg font-medium">{{ $tech }}</span>
              @endforeach
            </div>
          </div>
        @endif

        {{-- Result highlight --}}
        @if($project->result_summary)
          <div class="bg-green-50 border border-green-200 rounded-2xl p-6">
            <div class="text-3xl mb-3">📈</div>
            <h3 class="text-pm-navy font-extrabold font-heading mb-2">Key Result</h3>
            <p class="text-green-700 font-bold text-lg">{{ $project->result_summary }}</p>
          </div>
        @endif

        {{-- CTA card --}}
        <div class="bg-pm-navy rounded-2xl p-6 relative overflow-hidden">
          <div class="absolute inset-0 grid-overlay pointer-events-none rounded-2xl opacity-50"></div>
          <div class="relative z-10">
            <div class="badge-gold mb-4">Free Audit</div>
            <h3 class="text-white font-extrabold font-heading mb-3">Want Results Like This?</h3>
            <p class="text-gray-400 text-sm leading-relaxed mb-5">
              Book a free audit and we'll analyse your current setup and show you exactly what's possible.
            </p>
            <a href="{{ route('contact') }}" class="btn-primary w-full justify-center text-sm">
              Book Free Audit
            </a>
          </div>
        </div>

        {{-- Share --}}
        <div class="card">
          <h3 class="text-pm-navy font-extrabold font-heading mb-4 text-sm uppercase tracking-wider">Share</h3>
          <div class="flex gap-3">
            @php
              $shareUrl  = urlencode(url()->current());
              $shareText = urlencode($project->title . ' — Prosper Media Case Study');
            @endphp
            <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareText }}"
               target="_blank" rel="noopener"
               class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-[#1DA1F2]/10
                      text-[#1DA1F2] rounded-xl text-xs font-semibold hover:bg-[#1DA1F2]/20 transition-colors">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
              </svg>
              Twitter
            </a>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}"
               target="_blank" rel="noopener"
               class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-[#0A66C2]/10
                      text-[#0A66C2] rounded-xl text-xs font-semibold hover:bg-[#0A66C2]/20 transition-colors">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
              </svg>
              LinkedIn
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>
@endif

{{-- ── Gallery (if images exist) ───────────────────────────────── --}}
@if($project->gallery_images && count($project->gallery_images) > 0)
<section class="section-padding bg-pm-grey">
  <div class="container-custom">
    <h2 class="text-2xl font-extrabold text-pm-navy font-heading mb-8">Project Gallery</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      @foreach($project->gallery_images as $image)
        <div class="rounded-2xl overflow-hidden shadow-sm border border-gray-100 aspect-video">
          <img src="{{ Storage::url($image) }}"
               alt="{{ $project->title }} screenshot"
               class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- ── Bottom CTA ───────────────────────────────────────────────── --}}
<section class="bg-pm-grey py-16 border-t border-gray-100">
  <div class="container-custom">
    <div class="bg-pm-navy rounded-3xl p-10 md:p-16 text-center relative overflow-hidden">
      <div class="absolute inset-0 grid-overlay pointer-events-none rounded-3xl"></div>
      <div class="absolute top-0 right-0 w-64 h-64 bg-pm-cyan/10 rounded-full blur-3xl pointer-events-none"></div>
      <div class="relative z-10">
        <span class="badge-gold mb-6">Need Help Implementing This?</span>
        <h2 class="text-3xl md:text-4xl font-extrabold text-white font-heading mt-4 mb-4">
          Let's Build Something Like This<br>
          <span class="text-gradient">For Your Business</span>
        </h2>
        <p class="text-gray-400 text-lg mb-10 max-w-xl mx-auto">
          Book a free technical audit. We'll review your current setup and show you
          exactly what results are achievable.
        </p>
        <div class="flex flex-wrap items-center justify-center gap-4">
          <a href="{{ route('contact') }}" class="btn-gold text-base px-10 py-4">
            Get a Free Audit
          </a>
          <a href="{{ route('portfolio') }}" class="btn-secondary text-base px-10 py-4">
            View All Projects
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ── Related Projects ─────────────────────────────────────────── --}}
@if($related->count() > 0)
<section class="section-padding bg-white">
  <div class="container-custom">
    <h2 class="text-2xl font-extrabold text-pm-navy font-heading mb-10">Related Projects</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      @foreach($related as $rel)
        <div class="card group hover:-translate-y-1 transition-all duration-300">
          <div class="h-36 rounded-xl mb-4 bg-gradient-to-br from-pm-cyan/8 to-pm-navy/10
                      flex items-center justify-center relative overflow-hidden">
            @if($rel->featured_image)
              <img src="{{ Storage::url($rel->featured_image) }}" alt="{{ $rel->title }}"
                   class="w-full h-full object-cover rounded-xl">
            @else
              <svg class="w-10 h-10 text-pm-cyan/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
            @endif
          </div>
          <span class="badge-cyan text-[10px] mb-3">{{ $rel->category_label }}</span>
          <h3 class="text-pm-navy font-extrabold font-heading mb-2 text-sm leading-snug
                     group-hover:text-pm-cyan transition-colors">{{ $rel->title }}</h3>
          @if($rel->result_summary)
            <p class="text-green-600 text-xs font-semibold mb-3">📈 {{ $rel->result_summary }}</p>
          @endif
          <a href="{{ route('portfolio.show', $rel->slug) }}"
             class="text-pm-cyan text-xs font-bold hover:text-pm-navy transition-colors">
            View Case Study →
          </a>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

@endsection