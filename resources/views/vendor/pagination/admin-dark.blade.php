@if ($paginator->hasPages())
  <div class="flex items-center justify-between">
    <p class="text-gray-500 text-xs">
      {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}
      of {{ $paginator->total() }} results
    </p>

    <div class="flex items-center gap-1">
      {{-- Previous --}}
      @if ($paginator->onFirstPage())
        <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/3 text-gray-600 cursor-not-allowed">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </span>
      @else
        <a href="{{ $paginator->previousPageUrl() }}"
           class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 text-gray-400
                  hover:bg-white/10 hover:text-white transition-colors">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </a>
      @endif

      {{-- Page numbers --}}
      @foreach ($elements as $element)
        @if (is_string($element))
          <span class="w-8 h-8 flex items-center justify-center text-gray-600 text-xs">{{ $element }}</span>
        @endif
        @if (is_array($element))
          @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
              <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-pm-cyan text-white text-xs font-bold">
                {{ $page }}
              </span>
            @else
              <a href="{{ $url }}"
                 class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 text-gray-400 text-xs
                        hover:bg-white/10 hover:text-white transition-colors">
                {{ $page }}
              </a>
            @endif
          @endforeach
        @endif
      @endforeach

      {{-- Next --}}
      @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}"
           class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 text-gray-400
                  hover:bg-white/10 hover:text-white transition-colors">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </a>
      @else
        <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/3 text-gray-600 cursor-not-allowed">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </span>
      @endif
    </div>
  </div>
@endif