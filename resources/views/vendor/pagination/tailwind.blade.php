@if ($paginator->hasPages())
  <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between mt-8">

    {{-- Mobile --}}
    <div class="flex justify-between flex-1 sm:hidden">
      @if ($paginator->onFirstPage())
        <span class="px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-400 text-sm cursor-not-allowed">
          ← Previous
        </span>
      @else
        <a href="{{ $paginator->previousPageUrl() }}"
           class="px-4 py-2 rounded-xl bg-white border border-gray-200 text-pm-slate text-sm hover:border-pm-cyan hover:text-pm-cyan transition-colors">
          ← Previous
        </a>
      @endif

      @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}"
           class="px-4 py-2 rounded-xl bg-white border border-gray-200 text-pm-slate text-sm hover:border-pm-cyan hover:text-pm-cyan transition-colors">
          Next →
        </a>
      @else
        <span class="px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-400 text-sm cursor-not-allowed">
          Next →
        </span>
      @endif
    </div>

    {{-- Desktop --}}
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <p class="text-sm text-gray-500">
        Showing
        <span class="font-semibold text-pm-navy">{{ $paginator->firstItem() }}</span>
        to
        <span class="font-semibold text-pm-navy">{{ $paginator->lastItem() }}</span>
        of
        <span class="font-semibold text-pm-navy">{{ $paginator->total() }}</span>
        results
      </p>

      <div class="flex items-center gap-1">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
          <span class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-100 text-gray-300 cursor-not-allowed">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </span>
        @else
          <a href="{{ $paginator->previousPageUrl() }}"
             class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-pm-slate hover:border-pm-cyan hover:text-pm-cyan transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </a>
        @endif

        {{-- Page numbers --}}
        @foreach ($elements as $element)
          @if (is_string($element))
            <span class="w-9 h-9 flex items-center justify-center text-gray-400 text-sm">{{ $element }}</span>
          @endif
          @if (is_array($element))
            @foreach ($element as $page => $url)
              @if ($page == $paginator->currentPage())
                <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-pm-navy text-white text-sm font-semibold">
                  {{ $page }}
                </span>
              @else
                <a href="{{ $url }}"
                   class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-pm-slate text-sm hover:border-pm-cyan hover:text-pm-cyan transition-colors">
                  {{ $page }}
                </a>
              @endif
            @endforeach
          @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
          <a href="{{ $paginator->nextPageUrl() }}"
             class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-pm-slate hover:border-pm-cyan hover:text-pm-cyan transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </a>
        @else
          <span class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-100 text-gray-300 cursor-not-allowed">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </span>
        @endif
      </div>
    </div>
  </nav>
@endif