<?php

use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public bool   $open             = false;
    public string $draftCity        = '';
    public array  $draftInstruments = [];
    public array  $draftGenres      = [];

    public array $availableInstruments = ['Guitare', 'Basse', 'Batterie', 'Clavier', 'Violon', 'Chant', 'Saxophone', 'Trompette', 'Percussions'];
    public array $availableGenres      = ['Rock', 'Jazz', 'Pop', 'Folk', 'Metal', 'Classique', 'Electronic', 'Soul', 'Indie', 'Blues', 'World', 'Funk'];

    #[On('open-filter-drawer')]
    public function open(string $city = '', array $instruments = [], array $genres = []): void
    {
        $this->draftCity        = $city;
        $this->draftInstruments = $instruments;
        $this->draftGenres      = $genres;
        $this->open             = true;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function toggleInstrument(string $instrument): void
    {
        $this->draftInstruments = in_array($instrument, $this->draftInstruments)
            ? array_values(array_filter($this->draftInstruments, fn($i) => $i !== $instrument))
            : [...$this->draftInstruments, $instrument];
    }

    public function toggleGenre(string $genre): void
    {
        $this->draftGenres = in_array($genre, $this->draftGenres)
            ? array_values(array_filter($this->draftGenres, fn($g) => $g !== $genre))
            : [...$this->draftGenres, $genre];
    }

    public function clear(): void
    {
        $this->draftCity        = '';
        $this->draftInstruments = [];
        $this->draftGenres      = [];

        $this->dispatch('filters-applied',
            city:        '',
            instruments: [],
            genres:      [],
        );
    }

    public function apply(): void
    {
        $this->dispatch('filters-applied',
            city:        $this->draftCity,
            instruments: $this->draftInstruments,
            genres:      $this->draftGenres,
        );
        $this->open = false;
    }
};
?>

<div
    x-data
    x-init="$wire.$watch('open', val => document.body.style.overflow = val ? 'hidden' : '')"
>
    {{-- Backdrop --}}
    <div
        x-show="$wire.open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        wire:click="close"
        class="fixed inset-0 z-40 bg-dark/40 backdrop-blur-sm"
        style="display: none"
        aria-hidden="true"
    ></div>

    {{-- Panel --}}
    <div
        x-show="$wire.open"
        x-transition:enter="transform transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        @keydown.escape.window="$wire.close()"
        class="fixed inset-y-0 right-0 z-50 flex flex-col w-full md:w-[420px] bg-bg shadow-2xl"
        style="display: none"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('explore.filter_title') }}"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-dark/10 shrink-0">
            <div class="flex items-center gap-3">
                <h2 class="font-heading text-xl text-dark">{{ __('explore.filter_title') }}</h2>
                @if (count($draftInstruments) + count($draftGenres) + ($draftCity ? 1 : 0) > 0)
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-accent text-bg text-xs font-semibold">
                        {{ count($draftInstruments) + count($draftGenres) + ($draftCity ? 1 : 0) }}
                    </span>
                @endif
            </div>
            <button
                wire:click="close"
                type="button"
                class="w-9 h-9 flex items-center justify-center rounded-lg text-dark/40 hover:text-dark hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
                aria-label="{{ __('explore.filter_close') }}"
            >
                <x-icon name="x-mark" class="w-5 h-5" />
            </button>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto px-6 py-6 space-y-8">

            {{-- Ville --}}
            <section>
                <label for="drawer-city" class="block text-xs font-semibold uppercase tracking-widest text-dark/40 mb-3">
                    {{ __('explore.filter_city') }}
                </label>
                <input
                    id="drawer-city"
                    type="text"
                    wire:model.live.debounce.300ms="draftCity"
                    placeholder="{{ __('explore.filter_city') }}"
                    class="w-full px-4 py-3 rounded-[6px] bg-white border border-dark/15 text-base text-dark placeholder:text-dark/30 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-colors duration-150"
                />
            </section>

            {{-- Instruments --}}
            <section>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold uppercase tracking-widest text-dark/40">
                        {{ __('explore.filter_instruments') }}
                    </h3>
                    @if (count($draftInstruments) > 0)
                        <span class="text-xs text-accent font-semibold">{{ count($draftInstruments) }}</span>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2" role="group" :aria-label="'{{ __('explore.filter_instruments') }}'">
                    @foreach ($availableInstruments as $instr)
                        <button
                            type="button"
                            wire:click="toggleInstrument('{{ $instr }}')"
                            @class([
                                'h-9 px-4 rounded-full border text-sm font-medium transition-all duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent',
                                'bg-dark text-bg border-dark'                                                => in_array($instr, $draftInstruments),
                                'bg-white text-dark/60 border-dark/15 hover:border-dark/40 hover:text-dark' => !in_array($instr, $draftInstruments),
                            ])
                            aria-pressed="{{ in_array($instr, $draftInstruments) ? 'true' : 'false' }}"
                        >{{ $instr }}</button>
                    @endforeach
                </div>
            </section>

            {{-- Genres --}}
            <section>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold uppercase tracking-widest text-dark/40">
                        {{ __('explore.filter_genres') }}
                    </h3>
                    @if (count($draftGenres) > 0)
                        <span class="text-xs text-accent font-semibold">{{ count($draftGenres) }}</span>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2" role="group" :aria-label="'{{ __('explore.filter_genres') }}'">
                    @foreach ($availableGenres as $genre)
                        <button
                            type="button"
                            wire:click="toggleGenre('{{ $genre }}')"
                            @class([
                                'h-9 px-4 rounded-full border text-sm font-medium transition-all duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent',
                                'bg-accent text-bg border-accent'                                            => in_array($genre, $draftGenres),
                                'bg-white text-dark/60 border-dark/15 hover:border-dark/40 hover:text-dark' => !in_array($genre, $draftGenres),
                            ])
                            aria-pressed="{{ in_array($genre, $draftGenres) ? 'true' : 'false' }}"
                        >{{ $genre }}</button>
                    @endforeach
                </div>
            </section>

        </div>

        {{-- Footer --}}
        <div class="shrink-0 flex items-center gap-3 px-6 py-4 border-t border-dark/10 bg-bg">
            @if ($draftCity || count($draftInstruments) > 0 || count($draftGenres) > 0)
                <button
                    wire:click="clear"
                    type="button"
                    class="h-11 px-5 rounded-[6px] border border-dark/20 text-sm font-medium text-dark/60 hover:text-dark hover:border-dark/40 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
                >
                    {{ __('explore.filter_clear') }}
                </button>
            @endif
            <button
                wire:click="apply"
                type="button"
                class="flex-1 h-11 rounded-[6px] bg-dark text-bg text-sm font-medium hover:opacity-80 transition-opacity duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
            >
                {{ __('explore.filter_apply') }}
            </button>
        </div>
    </div>
</div>
