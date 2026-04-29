@props(['musiciansCount' => 0, 'announcementsCount' => 0])

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
    {{-- Sliding pill --}}
    <div
        x-ref="pill"
        wire:ignore
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
        <span class="ml-1.5 text-xs opacity-60 tabular-nums" aria-hidden="true">({{ $musiciansCount }})</span>
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
        <span class="ml-1.5 text-xs opacity-60 tabular-nums" aria-hidden="true">({{ $announcementsCount }})</span>
    </button>
</div>
