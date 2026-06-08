@props(['activeTab' => 'profiles', 'activeFiltersCount' => 0])

<div class="flex items-center gap-3">

    {{-- Publier une annonce --}}
    @if ($activeTab === 'announcements')
        <button
            x-data
            @auth @click="$wire.dispatch('open-modal', { component: 'parts.announcement.form', title: 'Publier une annonce' })" @endauth
            @guest @click="$dispatch('open-auth-modal')" @endguest
            type="button"
            class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-dark text-on-dark text-sm font-medium hover:opacity-80 transition-opacity duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent-on-dark"
        >
            <x-icon name="plus" class="w-4 h-4" />
            {{ __('explore.publish_cta') }}
        </button>
    @endif

    {{-- Filtres --}}
    <button
        @click="$wire.openFilterDrawer()"
        type="button"
        @class([
            'relative inline-flex items-center gap-2 h-10 px-4 rounded-xl border text-sm font-medium transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent',
            'border-dark text-body'                                                                  => $activeFiltersCount > 0,
            'border-dark/15 bg-bg text-subtle hover:text-body hover:border-dark/30'                => $activeFiltersCount === 0,
        ])
        aria-haspopup="dialog"
    >
        <x-icon name="sliders" class="w-4 h-4" />
        {{ __('explore.filter_title') }}
        @if ($activeFiltersCount > 0)
            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-accent text-on-dark text-xs font-semibold">
                {{ $activeFiltersCount }}
            </span>
        @endif
    </button>

</div>
