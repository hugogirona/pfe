@props(['activeFiltersCount' => 0])

<div class="flex items-center gap-3">

    {{-- Publier une annonce --}}
    <button
        x-show="activeTab === 'annonces'"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in-out duration-250"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="$wire.dispatch('open-modal', { component: 'parts.announcement.form', title: 'Publier une annonce' })"
        style="display: none"
        type="button"
        class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-dark text-bg text-sm font-medium hover:opacity-80 transition-opacity duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
    >
        <x-icon name="plus-circle" class="w-6 h-6" />
        {{ __('explore.publish_cta') }}
    </button>

    {{-- Filtres --}}
    <button
        @click="$wire.openFilterDrawer()"
        type="button"
        @class([
            'relative inline-flex items-center gap-2 h-10 px-4 rounded-xl border text-sm font-medium transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent',
            'border-dark text-dark'                                                                  => $activeFiltersCount > 0,
            'border-dark/15 bg-bg text-dark/60 hover:text-dark hover:border-dark/30'                => $activeFiltersCount === 0,
        ])
        aria-haspopup="dialog"
    >
        <x-icon name="sliders" class="w-4 h-4" />
        {{ __('explore.filter_title') }}
        @if ($activeFiltersCount > 0)
            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-accent text-bg text-xs font-semibold">
                {{ $activeFiltersCount }}
            </span>
        @endif
    </button>

</div>
