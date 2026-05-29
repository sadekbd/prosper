<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- SEO --}}
  <title>@yield('meta_title', 'Prosper Media — Engineering Digital Success with Technical Precision')</title>
  <meta name="description" content="@yield('meta_description', 'Tech-first Google Ads management, conversion tracking and high-performance web development agency.')">
  <link rel="canonical" href="{{ url()->current() }}">

  {{-- Open Graph --}}
  <meta property="og:type"        content="website">
  <meta property="og:url"         content="{{ url()->current() }}">
  <meta property="og:title"       content="@yield('og_title', 'Prosper Media')">
  <meta property="og:description" content="@yield('og_description', 'Engineering Digital Success with Technical Precision.')">
  <meta property="og:image"       content="@yield('og_image', asset('assets/images/og-default.jpg'))">
  <meta name="twitter:card"       content="summary_large_image">

  {{-- Favicon --}}
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%2300B4D8'/><text y='.9em' font-size='70' x='15' fill='white' font-weight='900'>P</text></svg>">

  {{-- Vite Assets --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])

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
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed bottom-6 right-6 z-[100] bg-green-500 text-white px-6 py-4 rounded-xl
                shadow-2xl flex items-center gap-3 max-w-sm">
      <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
      </svg>
      <p class="text-sm font-medium">{{ session('success') }}</p>
      <button @click="show = false" class="ml-auto opacity-70 hover:opacity-100">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
        </svg>
      </button>
    </div>
  @endif

  @stack('scripts')
</body>
</html>