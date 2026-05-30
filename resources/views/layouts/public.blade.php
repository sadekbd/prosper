<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Primary SEO --}}
  <title>@yield('meta_title', ($settings['default_meta_title'] ?? 'Prosper Media — Engineering Digital Success'))</title>
  <meta name="description" content="@yield('meta_description', ($settings['default_meta_description'] ?? 'Tech-first digital marketing agency for Google Ads, conversion tracking and web development.'))">
  <link rel="canonical" href="{{ url()->current() }}">

  {{-- Open Graph --}}
  <meta property="og:type"        content="@yield('og_type', 'website')">
  <meta property="og:url"         content="{{ url()->current() }}">
  <meta property="og:site_name"   content="Prosper Media">
  <meta property="og:title"       content="@yield('og_title', ($settings['default_meta_title'] ?? 'Prosper Media'))">
  <meta property="og:description" content="@yield('og_description', ($settings['default_meta_description'] ?? 'Engineering Digital Success with Technical Precision.'))">
  <meta property="og:image"       content="@yield('og_image', ($settings['og_image'] ? Storage::url($settings['og_image']) : asset('assets/og-default.jpg')))">
  <meta property="og:image:width"  content="1200">
  <meta property="og:image:height" content="630">

  {{-- Twitter Card --}}
  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:title"       content="@yield('meta_title', 'Prosper Media')">
  <meta name="twitter:description" content="@yield('meta_description', 'Engineering Digital Success with Technical Precision.')">
  <meta name="twitter:image"       content="@yield('og_image', asset('assets/og-default.jpg'))">

 {{-- Dynamic favicon from site settings --}}
@if(!empty($settings['favicon']))
  <link rel="icon" type="image/x-icon" href="{{ Storage::url($settings['favicon']) }}">
  <link rel="shortcut icon" href="{{ Storage::url($settings['favicon']) }}">
@else
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%2300B4D8'/><text y='.9em' font-size='70' x='15' fill='white' font-weight='900'>P</text></svg>">
@endif

  {{-- Fonts --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  {{-- Vite Assets --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  {{-- Google Analytics --}}
  @if(!empty($settings['google_analytics_id']))
  <script async src="https://www.googletagmanager.com/gtag/js?id={{ $settings['google_analytics_id'] }}"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ $settings['google_analytics_id'] }}');
  </script>
  @endif

  {{-- JSON-LD Structured Data --}}
  @include('components.public.seo-meta')

  @stack('head')
</head>
<body class="font-body text-pm-slate bg-pm-grey antialiased">

  @include('components.public.header')

  <main class="pt-20">
    @yield('content')
  </main>

  @include('components.public.footer')

  {{-- Toast Notifications --}}
  @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-end="opacity-0"
         class="fixed bottom-6 right-6 z-[100] bg-green-500 text-white px-6 py-4 rounded-xl
                shadow-2xl flex items-center gap-3 max-w-sm">
      <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
      </svg>
      <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
  @endif

  @stack('scripts') 
  
  <script src="{{ asset('js/prosper_chat_widget.js') }}?v=1"></script>
</body>
</html>