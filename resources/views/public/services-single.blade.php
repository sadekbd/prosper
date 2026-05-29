@extends('layouts.public')

@section('meta_title',       ($service->meta_title ?? $service->title . ' — Prosper Media'))
@section('meta_description', ($service->meta_description ?? $service->subtitle))

@section('content')

{{-- Hero --}}
<section class="bg-hero-gradient py-24 relative overflow-hidden">
  <div class="absolute inset-0 grid-overlay pointer-events-none"></div>
  <div class="container-custom relative z-10">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-8">
      <a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a>
      <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
      </svg>
      <a href="{{ route('services') }}" class="hover:text-white transition-colors">Services</a>
      <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
      </svg>
      <span class="text-white">{{ $service->title }}</span>
    </nav>

    <div class="max-w-3xl">
      <p class="section-label mb-4">Our Service</p>
      <h1 class="text-4xl md:text-5xl xl:text-6xl font-extrabold text-white font-heading leading-tight mb-5">
        {{ $service->title }}
      </h1>
      <p class="text-pm-cyan text-xl font-semibold mb-5">{{ $service->subtitle }}</p>
      <p class="text-gray-300 text-lg leading-relaxed">{{ $service->description }}</p>
    </div>
  </div>
</section>

{{-- Features + CTA --}}
<section class="section-padding bg-white">
  <div class="container-custom">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

      {{-- Features list --}}
      <div class="lg:col-span-2">
        <h2 class="text-2xl font-extrabold text-pm-navy font-heading mb-8">
          What's Included
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          @foreach($service->features as $feature)
            <div class="flex items-start gap-3 p-4 bg-pm-grey rounded-xl border border-gray-100
                        hover:border-pm-cyan/30 transition-colors">
              <div class="w-6 h-6 rounded-full bg-pm-cyan/15 flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-3.5 h-3.5 text-pm-cyan" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
              </div>
              <span class="text-pm-slate font-medium text-sm">{{ $feature->feature }}</span>
            </div>
          @endforeach
        </div>
      </div>

      {{-- Sidebar CTA --}}
      <div>
        <div class="bg-pm-navy rounded-2xl p-8 sticky top-28">
          <div class="absolute inset-0 grid-overlay pointer-events-none rounded-2xl opacity-50"></div>
          <div class="relative z-10">
            <div class="badge-gold mb-5">Free Audit</div>
            <h3 class="text-white font-extrabold font-heading text-xl mb-3">
              Get Started Today
            </h3>
            <p class="text-gray-400 text-sm leading-relaxed mb-6">
              Book a free audit and we'll review your current setup and show you exactly what's
              possible with {{ $service->title }}.
            </p>
            <a href="{{ route('contact') }}" class="btn-primary w-full justify-center text-sm mb-3">
              Book Free Audit
            </a>
            <a href="{{ route('services') }}" class="btn-secondary w-full justify-center text-sm">
              All Services
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Other services --}}
@if($others->count() > 0)
<section class="section-padding bg-pm-grey">
  <div class="container-custom">
    <h2 class="text-2xl font-extrabold text-pm-navy font-heading mb-10">Other Services</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      @foreach($others as $other)
        <div class="card-hover group">
          <h3 class="text-lg font-extrabold text-pm-navy font-heading mb-2
                     group-hover:text-pm-cyan transition-colors">{{ $other->title }}</h3>
          <p class="text-gray-500 text-sm mb-4">{{ $other->subtitle }}</p>
          <a href="{{ route('services.show', $other->slug) }}"
             class="text-pm-cyan text-sm font-bold hover:text-pm-navy transition-colors">
            Learn More →
          </a>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

@endsection