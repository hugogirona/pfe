<div class="flex items-center justify-between gap-4 flex-wrap">
    <div
        class="relative inline-flex bg-dark/[0.06] rounded-xl p-1 gap-1"
        role="tablist"
        x-init="
            const sync = () => {
                const btn = activeTab === 'musiciens' ? $refs.tabMusiciens : $refs.tabAnnonces;
                if (!btn || !$refs.pill) return;
                $refs.pill.style.left  = btn.offsetLeft + 'px';
                $refs.pill.style.width = btn.offsetWidth + 'px';
            };
            $nextTick(sync);
            $watch('activeTab', () => $nextTick(sync));
        "
    >
        {{-- Sliding rectangle --}}
        <div
            x-ref="pill"
            class="absolute top-1 bottom-1 rounded-lg bg-dark shadow-sm pointer-events-none"
            style="transition: left 200ms ease-out, width 200ms ease-out;"
        ></div>

        <button
            x-ref="tabMusiciens"
            @click="activeTab = 'musiciens'"
            :class="activeTab === 'musiciens' ? 'text-bg' : 'text-dark/50 hover:text-dark'"
            class="relative z-10 px-5 py-2 rounded-lg text-sm font-medium transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent"
            role="tab"
            :aria-selected="activeTab === 'musiciens'"
        >
            {{ __('explore.tab_musicians') }}
            <span
                class="ml-1.5 text-xs opacity-60 tabular-nums"
                x-text="'(' + filteredMusicians.length + ')'"
                aria-hidden="true"
            ></span>
        </button>

        <button
            x-ref="tabAnnonces"
            @click="activeTab = 'annonces'"
            :class="activeTab === 'annonces' ? 'text-bg' : 'text-dark/50 hover:text-dark'"
            class="relative z-10 px-5 py-2 rounded-lg text-sm font-medium transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent"
            role="tab"
            :aria-selected="activeTab === 'annonces'"
        >
            {{ __('explore.tab_announcements') }}
            <span
                class="ml-1.5 text-xs opacity-60 tabular-nums"
                x-text="'(' + filteredAnnouncements.length + ')'"
                aria-hidden="true"
            ></span>
        </button>
    </div>

    {{-- Filter btn --}}
    <button
        @click="drawerOpen = true"
        class="relative inline-flex items-center gap-2 h-10 px-4 rounded-xl border border-dark/15 bg-bg text-sm font-medium text-dark/60 hover:text-dark hover:border-dark/30 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
        :class="hasActiveFilters ? 'border-dark text-dark' : ''"
        aria-haspopup="dialog"
    >
        <x-icon name="sliders" class="w-4 h-4" />
        {{ __('explore.filter_title') }}
        <span
            x-show="activeFiltersCount > 0"
            x-text="activeFiltersCount"
            class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-accent text-bg text-xs font-semibold"
        ></span>
    </button>

</div>
