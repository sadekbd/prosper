@php $user = auth('admin')->user(); @endphp

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       class="fixed lg:relative z-30 flex-shrink-0 w-64 h-screen bg-pm-navy
              border-r border-white/5 flex flex-col
              transform transition-transform duration-300 ease-in-out overflow-y-auto">

  {{-- Logo --}}
<div class="flex items-center gap-3 px-6 py-5 border-b border-white/5 flex-shrink-0">

  @if(!empty($settings['site_logo']))
    {{-- Image logo from settings --}}
    <a href="{{ route('admin.dashboard') }}" class="flex items-center">
      <img src="{{ Storage::url($settings['site_logo']) }}"
           alt="{{ $settings['site_name'] ?? 'Prosper Media' }}"
           class="h-9 w-auto object-contain">
    </a>
  @else
    {{-- Fallback text logo --}}
    <div class="w-9 h-9 bg-pm-cyan rounded-lg flex items-center justify-center
                shadow-lg shadow-cyan-500/20 flex-shrink-0">
      <span class="text-white font-black font-heading">P</span>
    </div>
    <div class="leading-tight">
      <div class="text-white font-extrabold text-sm font-heading">
        Prosper<span class="text-pm-cyan">Media</span>
      </div>
      <div class="text-pm-gold text-[9px] font-bold tracking-widest uppercase">Admin Panel</div>
    </div>
  @endif

</div>

  {{-- User info --}}
  <div class="px-4 py-4 border-b border-white/5 flex-shrink-0">
    <div class="flex items-center gap-3 bg-white/5 rounded-xl px-3 py-2.5">
      <div class="w-8 h-8 bg-pm-cyan/20 rounded-lg flex items-center justify-center flex-shrink-0">
        <span class="text-pm-cyan font-bold text-sm font-heading">
          {{ strtoupper(substr($user->full_name, 0, 1)) }}
        </span>
      </div>
      <div class="min-w-0">
        <p class="text-white text-xs font-semibold truncate">{{ $user->full_name }}</p>
        <p class="text-gray-500 text-[10px] truncate">
          {{ ucfirst(str_replace('_', ' ', $user->role)) }}
        </p>
      </div>
    </div>
  </div>

  {{-- Navigation --}}
  <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

    @php
      $currentRoute = request()->route()?->getName() ?? '';

      /**
       * Nav definition — each item:
       * [route_name, label, svg_d_path, min_role ('all'|'admin'|'super')]
       *
       * 'all'   → any authenticated admin
       * 'admin' → admin + super_admin
       * 'super' → super_admin only
       */
      $navGroups = [
        'Overview' => [
          ['admin.dashboard',        'Dashboard',       'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'all'],
        ],
        'Content' => [
          ['admin.homepage.edit',     'Homepage',        'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'admin'],
          ['admin.blog.articles',     'Blog Articles',   'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'all'],
          ['admin.blog.categories',   'Categories',      'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 'admin'],
          ['admin.services',          'Services',        'M13 10V3L4 14h7v7l9-11h-7z', 'admin'],
          ['admin.portfolio',         'Portfolio',       'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'admin'],
        ],
        'Inbox' => [
          ['admin.messages',          'Contact Messages','M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'admin'],
          ['admin.newsletter',        'Newsletter',      'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 'admin'],
        ],
        'System' => [
          ['admin.users',             'Users',           'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'super'],
          ['admin.settings',          'Site Settings',   'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'super'],
        ],
      ];
    @endphp

    @foreach($navGroups as $groupLabel => $items)
      @php
        $visibleItems = collect($items)->filter(function($item) use ($user) {
          // Role gate
          $canSeeRole = match($item[3]) {
            'super'  => $user->role === 'super_admin',
            'admin'  => in_array($user->role, ['super_admin', 'admin']),
            default  => true,
          };
          // Route must exist — prevents crash if route not yet registered
          $routeExists = Route::has($item[0]);
          return $canSeeRole && $routeExists;
        });
      @endphp

      @if($visibleItems->isNotEmpty())
        <div class="mb-2">
          <p class="text-[9px] text-gray-600 uppercase tracking-[0.2em] font-bold px-3 mb-1">
            {{ $groupLabel }}
          </p>

          @foreach($visibleItems as [$routeName, $label, $icon, $access])
            @php
              // Highlight any route that starts with the same prefix
              $prefix    = explode('.', $routeName)[1] ?? '';
              $isActive  = str_contains($currentRoute, $prefix) && $currentRoute !== 'admin.dashboard'
                           ? true
                           : $currentRoute === $routeName;

              // New messages badge count
              $badgeCount = 0;
              if ($routeName === 'admin.messages' && class_exists(\App\Models\ContactMessage::class)) {
                try { $badgeCount = \App\Models\ContactMessage::where('status','new')->count(); }
                catch (\Exception $e) { $badgeCount = 0; }
              }
            @endphp

            <a href="{{ route($routeName) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                      transition-all duration-200 group
                      {{ $isActive
                        ? 'bg-pm-cyan/15 text-pm-cyan'
                        : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">

              <svg class="w-4 h-4 flex-shrink-0 transition-colors
                           {{ $isActive ? 'text-pm-cyan' : 'text-gray-500 group-hover:text-white' }}"
                   fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
              </svg>

              <span class="truncate">{{ $label }}</span>

              @if($badgeCount > 0)
                <span class="ml-auto bg-red-500 text-white text-[9px] font-bold
                             px-1.5 py-0.5 rounded-full leading-none">
                  {{ $badgeCount }}
                </span>
              @endif
            </a>
          @endforeach
        </div>
      @endif
    @endforeach

  </nav>

  {{-- Sign Out --}}
  <div class="px-3 py-4 border-t border-white/5 flex-shrink-0">
    <form action="{{ route('admin.logout') }}" method="POST">
      @csrf
      <button type="submit"
              class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                     text-gray-400 hover:bg-red-500/10 hover:text-red-400 transition-all group">
        <svg class="w-4 h-4 group-hover:text-red-400 transition-colors"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
        Sign Out
      </button>
    </form>
  </div>

</aside>
