@extends('layouts.public')

@section('meta_title',       'About Prosper Media — Our Story, Mission & Technical Expertise')
@section('meta_description', 'Prosper Media was built on the belief that marketing should be backed by solid technical foundations. Learn about our story, mission, and expertise.')

@section('content')

{{-- Hero --}}
<section class="bg-hero-gradient py-24 relative overflow-hidden">
  <div class="absolute inset-0 grid-overlay pointer-events-none"></div>
  <div class="container-custom relative z-10">
    <div class="max-w-3xl">
      <p class="section-label mb-4">About Us</p>
      <h1 class="text-4xl md:text-5xl xl:text-6xl font-extrabold text-white font-heading leading-tight mb-6">
        We Engineer Digital
        <span class="text-gradient"> Success.</span>
      </h1>
      <p class="text-gray-300 text-xl leading-relaxed max-w-2xl">
        Prosper Media was founded on the principle that digital marketing should be backed by solid
        technical foundations. In a crowded digital landscape, we help clients cut through the noise
        using a tech-first approach to marketing.
      </p>
    </div>
  </div>
</section>

{{-- Our Story --}}
<section class="section-padding bg-white">
  <div class="container-custom">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
      <div class="aos">
        <p class="section-label mb-3">Our Story</p>
        <h2 class="section-title mb-6">Built on Technical Foundations</h2>
        <p class="text-gray-500 leading-relaxed mb-5">
          Prosper Media was founded on the principle that digital marketing should be backed by solid
          technical foundations. We saw too many businesses running ads without proper tracking,
          building websites that looked great but didn't convert, and making decisions based on
          incomplete data.
        </p>
        <p class="text-gray-500 leading-relaxed mb-5">
          So we built a different kind of agency — one that starts with the technical setup first.
          Conversion tracking that actually works. Google Tag Manager configurations that capture
          every meaningful interaction. Server-side APIs that survive iOS changes.
        </p>
        <p class="text-gray-500 leading-relaxed mb-8">
          Today, we help ambitious businesses grow through a combination of precision tracking,
          high-performance advertising, and websites engineered to convert.
        </p>
        <a href="{{ route('contact') }}" class="btn-primary">
          Work With Us
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
          </svg>
        </a>
      </div>

      {{-- Mission & Values --}}
      <div class="space-y-6 aos">
        @foreach([
          ['Our Mission',    'bg-pm-cyan/10',  'text-pm-cyan',  'M13 10V3L4 14h7v7l9-11h-7z', 'To help businesses grow through technical precision, measurable marketing, and conversion-focused web development. Every strategy we build is rooted in data, every decision backed by evidence.'],
          ['Our Philosophy', 'bg-pm-gold/10',  'text-pm-gold',  'M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z', 'Be Optimistic. We believe every business deserves to grow. With the right technical foundation and strategic approach, sustainable digital growth is not a matter of luck — it\'s a matter of precision.'],
          ['Our Commitment', 'bg-pm-navy/5',   'text-pm-navy',  'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z', 'Transparent reporting, honest communication, and technical accountability. We treat your budget like our own and only recommend what genuinely moves your metrics.'],
        ] as [$heading, $bg, $col, $icon, $body])
          <div class="flex gap-5 p-6 bg-pm-grey rounded-2xl border border-gray-100 hover:border-pm-cyan/30 transition-colors">
            <div class="w-12 h-12 {{ $bg }} rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 {{ $col }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
              </svg>
            </div>
            <div>
              <h4 class="font-extrabold text-pm-navy font-heading mb-2">{{ $heading }}</h4>
              <p class="text-gray-500 text-sm leading-relaxed">{{ $body }}</p>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

{{-- Why Prosper Media --}}
<section class="section-padding bg-pm-grey">
  <div class="container-custom">
    <div class="text-center mb-16 aos">
      <p class="section-label mb-3">Why Prosper Media</p>
      <h2 class="section-title">The Difference Is In the Detail</h2>
      <p class="text-gray-500 text-lg mt-4 max-w-2xl mx-auto">
        We are not generalists. Every service we offer is underpinned by deep technical knowledge
        that most marketing agencies simply don't have.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      @foreach([
        ['🎯', 'Precision Tracking',    'We build tracking setups that capture every conversion, every event, and every customer journey touchpoint.'],
        ['📊', 'Data-Driven Strategy',  'Every recommendation is backed by real data. No guessing, no gut feelings — just measurable, repeatable results.'],
        ['⚡', 'Performance First',     'Fast websites, optimised campaigns, and clean code. We build for performance at every layer of the stack.'],
        ['🔒', 'Secure & Scalable',     'Enterprise-grade security practices on every project. Built to scale as your business grows.'],
      ] as [$emoji, $title, $desc])
        <div class="card-hover aos text-center">
          <div class="text-4xl mb-5">{{ $emoji }}</div>
          <h3 class="font-extrabold text-pm-navy font-heading mb-3">{{ $title }}</h3>
          <p class="text-gray-500 text-sm leading-relaxed">{{ $desc }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- Skills & Expertise --}}
<section class="section-padding bg-white">
  <div class="container-custom">
    <div class="text-center mb-16 aos">
      <p class="section-label mb-3">Our Expertise</p>
      <h2 class="section-title">Technical Skills That Drive Results</h2>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
      @foreach([
        ['PPC Strategy',              '95', '#00B4D8'],
        ['Conversion Tracking',       '98', '#FFB703'],
        ['Web Development',           '90', '#00B4D8'],
        ['Analytics & Reporting',     '93', '#FFB703'],
        ['Technical SEO',             '85', '#00B4D8'],
        ['Landing Page Optimization', '92', '#FFB703'],
      ] as [$skill, $pct, $color])
        <div class="card-hover aos text-center">
          {{-- Circular progress --}}
          <div class="relative w-20 h-20 mx-auto mb-4">
            <svg class="w-20 h-20 -rotate-90" viewBox="0 0 80 80">
              <circle cx="40" cy="40" r="32" fill="none" stroke="#F8F9FA" stroke-width="6"/>
              <circle cx="40" cy="40" r="32" fill="none"
                      stroke="{{ $color }}" stroke-width="6"
                      stroke-linecap="round"
                      stroke-dasharray="{{ 2 * 3.14159 * 32 }}"
                      stroke-dashoffset="{{ 2 * 3.14159 * 32 * (1 - $pct / 100) }}"
                      style="transition: stroke-dashoffset 1.5s ease"/>
            </svg>
            <div class="absolute inset-0 flex items-center justify-center">
              <span class="text-sm font-extrabold text-pm-navy font-heading">{{ $pct }}%</span>
            </div>
          </div>
          <p class="text-xs font-bold text-pm-slate text-center leading-tight">{{ $skill }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- CTA --}}
<section class="bg-cta-gradient py-20 relative overflow-hidden">
  <div class="absolute inset-0 dot-overlay pointer-events-none opacity-40"></div>
  <div class="container-custom relative z-10 text-center">
    <h2 class="text-3xl md:text-4xl font-extrabold text-white font-heading mb-4">
      Ready to Work with a Tech-First Agency?
    </h2>
    <p class="text-gray-400 text-lg mb-8 max-w-xl mx-auto">
      Let's talk about your goals and build a strategy backed by real technical precision.
    </p>
    <a href="{{ route('contact') }}" class="btn-primary text-base px-10 py-4">
      Get a Free Audit Today
    </a>
  </div>
</section>

@endsection