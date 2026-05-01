@if ($paginator->hasPages())
    <nav
        role="navigation"
        aria-label="{{ __('Pagination Navigation') }}"
        class="flex items-center justify-center gap-1"
    >
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span
                aria-disabled="true"
                class="w-9 h-9 flex items-center justify-center rounded-full text-dark/20 cursor-not-allowed"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </span>
        @else
            <a
                href="{{ $paginator->previousPageUrl() }}"
                rel="prev"
                aria-label="{{ __('pagination.previous') }}"
                class="w-9 h-9 flex items-center justify-center rounded-full text-dark/50 hover:text-dark hover:bg-dark/5 transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/40"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </a>
        @endif

        {{-- Pages --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="w-9 h-9 flex items-center justify-center text-sm text-dark/30 select-none">
                    {{ $element }}
                </span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span
                            aria-current="page"
                            class="w-9 h-9 flex items-center justify-center rounded-full bg-accent text-white text-sm font-semibold select-none"
                        >{{ $page }}</span>
                    @else
                        <a
                            href="{{ $url }}"
                            aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                            class="w-9 h-9 flex items-center justify-center rounded-full text-sm text-dark/60 hover:text-dark hover:bg-dark/5 transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/40"
                        >{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a
                href="{{ $paginator->nextPageUrl() }}"
                rel="next"
                aria-label="{{ __('pagination.next') }}"
                class="w-9 h-9 flex items-center justify-center rounded-full text-dark/50 hover:text-dark hover:bg-dark/5 transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/40"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        @else
            <span
                aria-disabled="true"
                class="w-9 h-9 flex items-center justify-center rounded-full text-dark/20 cursor-not-allowed"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </span>
        @endif
    </nav>
@endif