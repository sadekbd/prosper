@php $user = auth('admin')->user(); @endphp

<header class="flex-shrink-0 h-16 bg-pm-navy/80 border-b border-white/5
               backdrop-blur-md flex items-center justify-between px-6 lg:px-8 gap-4">

  {{-- Left: hamburger + page title --}}
  <div class="flex items-center gap-4">
    {{-- Mobile hamburger --}}
    <button @click="sidebarOpen = !sidebarOpen"
            class="lg:hidden text-gray-400 hover:text-white transition-colors p-1">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>

    <div>
      <h1 class="text-white font-bold text-sm font-heading">
        @yield('page_title', 'Dashboard')
      </h1>
      <p class="text-gray-500 text-xs hidden sm:block">
        @yield('page_subtitle', 'Prosper Media Admin Panel')
      </p>
    </div>
  </div>

  {{-- Right: actions + user --}}
  <div class="flex items-center gap-3">

    {{-- View website --}}
    <a href="{{ route('home') }}" target="_blank"
       class="hidden sm:flex items-center gap-2 text-gray-400 hover:text-pm-cyan
              text-xs font-medium transition-colors px-3 py-2 rounded-lg hover:bg-white/5">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
      </svg>
      View Site
    </a>

    {{-- New message notification — only if route exists and user has access --}}
    @if(Route::has('admin.messages') && in_array($user->role, ['super_admin', 'admin']))
      @php
        $newMsgCount = 0;
        try {
          $newMsgCount = \App\Models\ContactMessage::where('status', 'new')->count();
        } catch (\Exception $e) {}
      @endphp

      @if($newMsgCount > 0)
        <a href="{{ route('admin.messages') }}"
           class="relative p-2 text-gray-400 hover:text-white transition-colors
                  rounded-lg hover:bg-white/5"
           title="{{ $newMsgCount }} new message{{ $newMsgCount > 1 ? 's' : '' }}">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
          <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
        </a>
      @endif
    @endif

    {{-- User dropdown --}}
    <div x-data="{ open: false }" class="relative">
      <button @click="open = !open" @click.away="open = false"
              class="flex items-center gap-2 group">
        <div class="w-8 h-8 bg-pm-cyan/20 rounded-lg flex items-center justify-center
                    border border-pm-cyan/20 hover:border-pm-cyan/40 transition-colors">
          <span class="text-pm-cyan font-bold text-sm font-heading">
            {{ strtoupper(substr($user->full_name, 0, 1)) }}
          </span>
        </div>
        <div class="hidden md:block text-left">
          <p class="text-white text-xs font-semibold leading-none">
            {{ Str::limit($user->full_name, 20) }}
          </p>
          <p class="text-gray-500 text-[10px] leading-none mt-0.5">
            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
          </p>
        </div>
        <svg class="w-3 h-3 text-gray-500 hidden md:block" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
        </svg>
      </button>

      <div x-show="open" x-cloak x-transition
           class="absolute right-0 mt-2 w-52 bg-[#1a2540] border border-white/10
                  rounded-xl shadow-2xl overflow-hidden z-50">
        <div class="px-4 py-3 border-b border-white/5">
          <p class="text-white text-xs font-semibold">{{ $user->full_name }}</p>
          <p class="text-gray-400 text-[10px] truncate">{{ $user->email }}</p>
          <span class="inline-block mt-1.5 px-2 py-0.5 rounded-md text-[9px] font-bold
                       bg-pm-cyan/10 text-pm-cyan uppercase tracking-wide">
            {{ str_replace('_', ' ', $user->role) }}
          </span>
        </div>

        {{-- Last login info --}}
        @if($user->last_login_at)
          <div class="px-4 py-2 border-b border-white/5">
            <p class="text-gray-500 text-[10px]">
              Last login: {{ $user->last_login_at->diffForHumans() }}
            </p>
          </div>
        @endif

        <form action="{{ route('admin.logout') }}" method="POST">
          @csrf
          <button type="submit"
                  class="w-full flex items-center gap-3 px-4 py-3 text-red-400
                         hover:bg-red-500/10 transition-colors text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Sign Out
          </button>
        </form>
      </div>
    </div>

  </div>
</header>