@props(['instruments', 'genres'])

<div
    x-show="drawerOpen"
    style="display:none"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="drawerOpen = false"
    class="fixed inset-0 z-40 bg-dark/40 backdrop-blur-sm"
    aria-hidden="true"
></div>


<div
    x-show="drawerOpen"
    style="display:none"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    class="fixed inset-y-0 right-0 z-50 flex flex-col w-full md:w-[420px] bg-bg shadow-2xl"
    role="dialog"
    aria-modal="true"
    aria-label="{{ __('explore.filter_title') }}"
    @keydown.escape.window="drawerOpen = false"
>

    <section  class="flex items-center justify-between px-6 py-5 border-b border-dark/10 shrink-0">
        <div class="flex items-center gap-3">
            <h2 class="font-heading text-xl text-dark">{{ __('explore.filter_title') }}</h2>
            <span
                x-show="activeFiltersCount > 0"
                x-text="activeFiltersCount"
                class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-accent text-bg text-xs font-semibold"
            ></span>
        </div>
        <button
            @click="drawerOpen = false"
            class="w-9 h-9 flex items-center justify-center rounded-lg text-dark/40 hover:text-dark hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
            aria-label="{{ __('explore.filter_close') }}"
        >
            <x-icon name="x-mark" class="w-5 h-5" />
        </button>
    </section>

    <div class="flex-1 overflow-y-auto px-6 py-6 space-y-8">

        {{-- City --}}
        <section>
            <label for="drawer-city" class="block text-xs font-semibold uppercase tracking-widest text-dark/40 mb-3">
                {{ __('explore.filter_city') }}
            </label>
            <x-search-bar class="min-w-0" icon="map-pin" />
        </section>

        {{-- Instruments --}}
        <section>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold uppercase tracking-widest text-dark/40">
                    {{ __('explore.filter_instruments') }}
                </h3>
                <span
                    x-show="activeInstruments.length > 0"
                    x-text="activeInstruments.length"
                    class="text-xs text-accent font-semibold"
                ></span>
            </div>
            <div class="flex flex-wrap gap-2" role="group" :aria-label="'{{ __('explore.filter_instruments') }}'">
                @foreach ($instruments as $instr)
                    <button
                        @click="toggleInstrument('{{ $instr }}')"
                        :class="activeInstruments.includes('{{ $instr }}')
                            ? 'bg-dark text-bg border-dark'
                            : 'bg-white text-dark/60 border-dark/15 hover:border-dark/40 hover:text-dark'"
                        class="h-9 px-4 rounded-full border text-sm font-medium transition-all duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
                        :aria-pressed="activeInstruments.includes('{{ $instr }}')"
                    >
                        {{ $instr }}
                    </button>
                @endforeach
            </div>
        </section>

        {{-- Genres --}}
        <section>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold uppercase tracking-widest text-dark/40">
                    {{ __('explore.filter_genres') }}
                </h3>
                <span
                    x-show="activeGenres.length > 0"
                    x-text="activeGenres.length"
                    class="text-xs text-accent font-semibold"
                ></span>
            </div>
            <div class="flex flex-wrap gap-2" role="group" :aria-label="'{{ __('explore.filter_genres') }}'">
                @foreach ($genres as $genre)
                    <button
                        @click="toggleGenre('{{ $genre }}')"
                        :class="activeGenres.includes('{{ $genre }}')
                            ? 'bg-accent text-bg border-accent'
                            : 'bg-white text-dark/60 border-dark/15 hover:border-dark/40 hover:text-dark'"
                        class="h-9 px-4 rounded-full border text-sm font-medium transition-all duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
                        :aria-pressed="activeGenres.includes('{{ $genre }}')"
                    >
                        {{ $genre }}
                    </button>
                @endforeach
            </div>
        </section>

    </div>

    {{-- Footer --}}
    <div class="shrink-0 flex items-center gap-3 px-6 py-4 border-t border-dark/10 bg-bg">
        <button
            @click="clearFilters()"
            x-show="hasActiveFilters"
            class="h-11 px-5 rounded-[6px] border border-dark/20 text-sm font-medium text-dark/60 hover:text-dark hover:border-dark/40 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
        >
            {{ __('explore.filter_clear') }}
        </button>
        <button
            @click="drawerOpen = false"
            class="flex-1 h-11 rounded-[6px] bg-dark text-bg text-sm font-medium hover:opacity-80 transition-opacity duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
        >
            <span x-text="activeTab === 'musiciens'
                ? '{{ __('explore.filter_see') }} ' + filteredMusicians.length + ' {{ __('explore.tab_musicians_lc') }}'
                : '{{ __('explore.filter_see') }} ' + filteredAnnouncements.length + ' {{ __('explore.tab_announcements_lc') }}'">
            </span>
        </button>
    </div>

</div>
