<header
  id="site-header"
  x-data="{ open: false, scrolled: false }"
  @scroll.window="scrolled = window.scrollY > 60"
  :class="scrolled
    ? 'bg-pm-navy/95 backdrop-blur-md shadow-2xl shadow-black/20'
    : 'bg-pm-navy'"
  class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
>
  <div class="container-custom">
    <div class="flex items-center justify-between h-20">

      {{-- ── Logo ── --}}
      <a href="{{ route('home') }}" class="flex items-center gap-3 group flex-shrink-0">

        @if(!empty($settings['site_logo']))
          {{-- Dynamic image logo from admin settings --}}
          <img src="{{ Storage::url($settings['site_logo']) }}"
               alt="{{ $settings['site_name'] ?? 'Prosper Media' }}"
               class="h-10 w-auto object-contain group-hover:scale-105 transition-transform duration-200">
        @else
          {{-- Fallback text logo --}}
          <div class="w-10 h-10 bg-pm-cyan rounded-xl flex items-center justify-center
                      group-hover:scale-105 transition-transform duration-200 shadow-lg shadow-cyan-500/30 flex-shrink-0">
            <span class="text-white font-black text-xl font-heading leading-none">P</span>
          </div>
          <div class="leading-tight">
            <div class="text-white font-extrabold text-lg font-heading tracking-tight leading-none">
              Prosper<span class="text-pm-cyan">Media</span>
            </div>
            <div class="text-pm-gold text-[10px] font-bold tracking-[0.2em] uppercase leading-none mt-0.5">
              Be Optimistic
            </div>
          </div>
        @endif

      </a>

      {{-- ── Desktop Nav ── --}}
      <nav class="hidden lg:flex items-center gap-1">
        @php
          $links = [
            ['Home',      'home'],
            ['About',     'about'],
            ['Services',  'services'],
            ['Portfolio', 'portfolio'],
            ['Blog',      'blog'],
            ['Contact',   'contact'],
          ];
        @endphp
        @foreach($links as [$label, $route])
          <a href="{{ route($route) }}"
             class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white rounded-lg
                    hover:bg-white/10 transition-all duration-200 relative group
                    {{ request()->routeIs($route) ? 'text-white bg-white/10' : '' }}">
            {{ $label }}
            <span class="absolute bottom-1 left-1/2 -translate-x-1/2 w-0 h-0.5 bg-pm-cyan rounded-full
                         group-hover:w-4 transition-all duration-300
                         {{ request()->routeIs($route) ? 'w-4' : '' }}"></span>
          </a>
        @endforeach
      </nav>

      {{-- ── Right: CTA + Dark Mode Toggle ── --}}
      <div class="hidden lg:flex items-center gap-3">

        {{-- Dark/Light Mode Toggle --}}
        <button id="theme-toggle"
                onclick="toggleTheme()"
                title="Toggle dark/light mode"
                class="p-2.5 text-gray-400 hover:text-white rounded-xl hover:bg-white/10
                       transition-all duration-200 relative">
          {{-- Sun icon — shown in dark mode --}}
          <svg id="icon-sun" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>
          </svg>
          {{-- Moon icon — shown in light mode --}}
          <svg id="icon-moon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
          </svg>
        </button>

        {{-- CTA --}}
        <a href="{{ route('contact') }}" class="btn-primary text-sm py-2.5 px-5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
          Get a Free Audit
        </a>
      </div>

      {{-- ── Mobile: toggle + burger ── --}}
      <div class="lg:hidden flex items-center gap-2">
        {{-- Mobile dark mode toggle --}}
        <button onclick="toggleTheme()"
                class="p-2 text-gray-400 hover:text-white rounded-lg hover:bg-white/10 transition-colors">
          <svg id="icon-sun-mob" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>
          </svg>
          <svg id="icon-moon-mob" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
          </svg>
        </button>

        {{-- Hamburger --}}
        <button @click="open = !open"
                class="p-2 text-white rounded-lg hover:bg-white/10 transition-colors"
                aria-label="Toggle navigation">
          <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
          <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

    </div>
  </div>

  {{-- ── Mobile Menu ── --}}
  <div x-show="open" x-cloak
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0 -translate-y-3"
       x-transition:enter-end="opacity-100 translate-y-0"
       x-transition:leave="transition ease-in duration-150"
       x-transition:leave-end="opacity-0 -translate-y-3"
       class="lg:hidden bg-[#0a1628] border-t border-white/10">
    <div class="container-custom py-6 flex flex-col gap-1">
      @foreach($links as [$label, $route])
        <a href="{{ route($route) }}" @click="open = false"
           class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:text-white
                  hover:bg-white/5 rounded-xl transition-all text-sm font-medium
                  {{ request()->routeIs($route) ? 'text-pm-cyan bg-pm-cyan/5' : '' }}">
          <span class="w-1.5 h-1.5 rounded-full bg-pm-cyan
                        {{ request()->routeIs($route) ? 'opacity-100' : 'opacity-30' }}"></span>
          {{ $label }}
        </a>
      @endforeach
      <div class="mt-4 pt-4 border-t border-white/10">
        <a href="{{ route('contact') }}" class="btn-primary w-full justify-center text-sm">
          Get a Free Audit
        </a>
      </div>
    </div>
  </div>
</header>