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
        <div class="w-10 h-10 bg-pm-cyan rounded-xl flex items-center justify-center
                    group-hover:scale-105 transition-transform duration-200 shadow-lg shadow-cyan-500/30">
          <span class="text-white font-black text-xl font-heading leading-none">P</span>
        </div>
        <div class="leading-tight">
          <div class="text-white font-extrabold text-lg font-heading tracking-tight leading-none">
            Prosper<span class="text-pm-cyan">Media</span>
          </div>
          <div class="text-pm-gold text-[10px] font-semibold tracking-[0.2em] uppercase leading-none mt-0.5">
            Be Optimistic
          </div>
        </div>
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

      {{-- ── CTA Button ── --}}
      <div class="hidden lg:flex items-center gap-3">
        <a href="{{ route('contact') }}" class="btn-primary text-sm py-2.5 px-5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
          Get a Free Audit
        </a>
      </div>

      {{-- ── Mobile Burger ── --}}
      <button @click="open = !open"
              class="lg:hidden p-2 text-white rounded-lg hover:bg-white/10 transition-colors"
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

  {{-- ── Mobile Menu ── --}}
  <div x-show="open" x-cloak
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0 -translate-y-3"
       x-transition:enter-end="opacity-100 translate-y-0"
       x-transition:leave="transition ease-in duration-150"
       x-transition:leave-start="opacity-100 translate-y-0"
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