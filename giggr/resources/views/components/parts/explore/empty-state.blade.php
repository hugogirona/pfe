<div class="flex flex-col items-center justify-center py-24 px-6 text-center">
    <x-icon name="music-note" class="w-14 h-14 text-dark/15 mb-6" />
    <p class="font-heading text-2xl text-dark mb-2">{{ __('explore.no_results_title') }}</p>
    <p class="text-base text-dark/50 max-w-sm mb-8">{{ __('explore.no_results_text') }}</p>
    <button
        @click="clearFilters()"
        class="inline-flex items-center px-6 py-2.5 rounded-[6px] border border-dark/20 text-sm font-medium text-dark hover:bg-dark hover:text-bg hover:border-dark transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
    >
        {{ __('explore.filter_clear') }}
    </button>
</div>
