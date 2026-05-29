<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Admin') — Prosper Media</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%2300B4D8'/><text y='.9em' font-size='70' x='15' fill='white' font-weight='900'>P</text></svg>">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body bg-pm-navy antialiased min-h-screen">

  {{-- Background pattern --}}
  <div class="fixed inset-0 grid-overlay pointer-events-none opacity-50"></div>
  <div class="fixed top-0 right-0 w-96 h-96 bg-pm-cyan/5 rounded-full blur-3xl pointer-events-none"></div>
  <div class="fixed bottom-0 left-0 w-80 h-80 bg-pm-gold/3 rounded-full blur-3xl pointer-events-none"></div>

  <div class="relative z-10 min-h-screen flex flex-col">

    {{-- Logo header --}}
    <div class="flex justify-center pt-10 pb-6">
      <a href="{{ route('home') }}" class="flex items-center gap-3 group">
        <div class="w-11 h-11 bg-pm-cyan rounded-xl flex items-center justify-center shadow-lg shadow-cyan-500/25 group-hover:scale-105 transition-transform">
          <span class="text-white font-black text-xl font-heading">P</span>
        </div>
        <div>
          <div class="text-white font-extrabold text-xl font-heading tracking-tight">
            Prosper<span class="text-pm-cyan">Media</span>
          </div>
          <div class="text-pm-gold text-[10px] font-bold tracking-[0.25em] uppercase">Be Optimistic</div>
        </div>
      </a>
    </div>

    {{-- Card --}}
    <div class="flex-1 flex items-start justify-center px-4 pb-12">
      <div class="w-full max-w-md">

        {{-- Flash messages --}}
        @if(session('success'))
          <div class="mb-5 bg-green-500/10 border border-green-500/30 text-green-400
                      rounded-xl px-5 py-4 text-sm flex items-start gap-3">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
          </div>
        @endif

        @if(session('error'))
          <div class="mb-5 bg-red-500/10 border border-red-500/30 text-red-400
                      rounded-xl px-5 py-4 text-sm flex items-start gap-3">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
          </div>
        @endif

        {{-- Page content --}}
        <div class="bg-white/5 border border-white/10 rounded-2xl p-8 backdrop-blur-sm shadow-2xl">
          @yield('content')
        </div>

        {{-- Footer links --}}
        <div class="text-center mt-6 text-gray-500 text-xs">
          <a href="{{ route('home') }}" class="hover:text-pm-cyan transition-colors">← Back to website</a>
          &nbsp;·&nbsp;
          <a href="{{ route('privacy-policy') }}" class="hover:text-pm-cyan transition-colors">Privacy Policy</a>
        </div>
      </div>
    </div>
  </div>

</body>
</html>