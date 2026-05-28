@props(['activeTab' => 'profils', 'activeFiltersCount' => 0])

<div class="flex items-center gap-3">

    {{-- Publier une annonce --}}
    @auth
        @if ($activeTab === 'annonces')
            <button
                @click="$wire.dispatch('open-modal', { component: 'parts.announcement.form', title: 'Publier une annonce' })"
                type="button"
                class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-dark text-bg text-sm font-medium hover:opacity-80 transition-opacity duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
            >
                <x-icon name="plus" class="w-4 h-4" />
                {{ __('explore.publish_cta') }}
            </button>
        @endif
    @endauth

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
