<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') — {{ $settings['site_name'] ?? 'Prosper Media' }} Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  
  {{-- Dynamic favicon from site settings --}}

@if(!empty($settings['favicon']))
  <link rel="icon" type="image/x-icon" href="{{ Storage::url($settings['favicon']) }}">
@else
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%2300B4D8'/><text y='.9em' font-size='70' x='15' fill='white' font-weight='900'>P</text></svg>">
@endif

@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body bg-[#0f1623] antialiased"
      x-data="{ sidebarOpen: window.innerWidth >= 1024 }"
      @resize.window="sidebarOpen = window.innerWidth >= 1024">

  <div class="flex h-screen overflow-hidden">

    {{-- ── Sidebar ── --}}
    @include('components.admin.sidebar')

    {{-- ── Main ── --}}
    <div class="flex-1 flex flex-col overflow-hidden">

      {{-- Topbar --}}
      @include('components.admin.topbar')

      {{-- Content --}}
      <main class="flex-1 overflow-y-auto bg-[#0f1623] p-6 lg:p-8">

        {{-- Flash messages --}}
        @if(session('success'))
          <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
               x-transition class="mb-6 bg-green-500/10 border border-green-500/20 text-green-400
               rounded-xl px-5 py-4 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
          </div>
        @endif

        @if(session('error'))
          <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
               x-transition class="mb-6 bg-red-500/10 border border-red-500/20 text-red-400
               rounded-xl px-5 py-4 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
          </div>
        @endif

        @yield('content')
      </main>
    </div>
  </div>

  {{-- Mobile sidebar overlay --}}
  <div x-show="sidebarOpen && window.innerWidth < 1024"
       x-cloak
       @click="sidebarOpen = false"
       class="fixed inset-0 bg-black/60 backdrop-blur-sm z-20 lg:hidden">
  </div>

  @stack('scripts')
</body>
</html>