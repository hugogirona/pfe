<?php

use App\Enums\AnnouncementType;
use App\Models\Genre;
use App\Models\Instrument;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public bool $open = false;
    public string $activeTab = 'profiles';
    public ?int $draftCityId = null;
    public int $draftRadius = 0;
    public array $draftInstruments = [];
    public array $draftGenres = [];
    public array $draftTypes = [];
    public bool $draftFollowing = false;

    public int $pickerKey = 0;

    public array $availableInstruments = [];
    public array $availableGenres = [];
    /** @var array<int, array{value:string, label:string}> */
    public array $availableTypes = [];

    public function mount(): void
    {
        $this->availableInstruments = Instrument::orderBy('name')->pluck('name')->toArray();
        $this->availableGenres = Genre::orderBy('name')->pluck('name')->toArray();
        $this->availableTypes = collect(AnnouncementType::cases())
            ->map(fn ($case) => ['value' => $case->value, 'label' => __($case->label())])
            ->all();
    }

    #[On('open-filter-drawer')]
    public function open(?int $cityId = null, int $radius = 0, array $instruments = [], array $genres = [], array $types = [], bool $following = false, string $activeTab = 'profiles'): void
    {
        $this->draftCityId = $cityId;
        $this->draftRadius = $radius;
        $this->draftInstruments = $instruments;
        $this->draftGenres = $genres;
        $this->draftTypes = $types;
        $this->draftFollowing = $following;
        $this->activeTab = $activeTab;
        $this->open = true;
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

    public function toggleType(string $type): void
    {
        $this->draftTypes = in_array($type, $this->draftTypes)
            ? array_values(array_filter($this->draftTypes, fn($t) => $t !== $type))
            : [...$this->draftTypes, $type];
    }

    public function clear(): void
    {
        $this->draftCityId = null;
        $this->draftRadius = 0;
        $this->draftInstruments = [];
        $this->draftGenres = [];
        $this->draftTypes = [];
        $this->draftFollowing = false;
        // This is for the reset of the input after "clear filters" button
        $this->pickerKey++;

        $this->dispatch('filters-applied',
            cityId: null,
            radius: 0,
            instruments: [],
            genres: [],
            types: [],
            following: false,
        );
    }

    public function apply(): void
    {
        $this->dispatch('filters-applied',
            cityId: $this->draftCityId,
            radius: $this->draftRadius,
            instruments: $this->draftInstruments,
            genres: $this->draftGenres,
            types: $this->draftTypes,
            following: $this->draftFollowing,
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
        class="fixed inset-y-0 right-0 z-50 flex flex-col w-full md:w-105 bg-bg shadow-2xl"
        style="display: none"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('explore.filter_title') }}"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-dark/10 shrink-0">
            <div class="flex items-center gap-3">
                <h2 class="font-heading text-xl text-heading">{{ __('explore.filter_title') }}</h2>
                @php $activeDraft = count($draftInstruments) + count($draftGenres) + count($draftTypes) + ($draftCityId !== null ? 1 : 0) + ($draftCityId !== null && $draftRadius > 0 ? 1 : 0) + ($draftFollowing ? 1 : 0); @endphp
                @if ($activeDraft > 0)
                    <span
                        class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-accent text-on-dark text-xs font-semibold">
                        {{ $activeDraft }}
                    </span>
                @endif
            </div>
            <button
                wire:click="close"
                type="button"
                class="w-9 h-9 flex items-center justify-center rounded-lg text-caption hover:text-body hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
                aria-label="{{ __('explore.filter_close') }}"
            >
                <x-icon name="x-mark" class="w-5 h-5"/>
            </button>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto px-6 py-6 space-y-8">

            {{-- Ville --}}
            <section>
                <livewire:parts.form.locality-picker
                    wire:model.live="draftCityId"
                    :label="__('explore.filter_city')"
                    :required="false"
                    :filter-style="true"
                    :wire:key="'drawer-locality-' . $pickerKey"
                />
            </section>

            <x-parts.explore.radius-slider
                model="draftRadius"
                :disabled="$draftCityId === null"
            />

            @if ($activeTab === 'announcements')
                <x-parts.explore.type-filter
                    :types="$availableTypes"
                    :selected="$draftTypes"
                />
            @endif

            @auth
                {{-- Comptes suivis --}}
                <section>
                    <h3 class="text-xs font-semibold uppercase tracking-widest text-caption mb-3">
                        {{ __('explore.filter_following_label') }}
                    </h3>
                    <x-form.checkbox name="drawer-following" wire:model.live="draftFollowing">
                        {{ __('explore.filter_following') }}
                    </x-form.checkbox>
                </section>
            @endauth

            {{-- Instruments --}}
            <section>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold uppercase tracking-widest text-caption">
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
                                'bg-dark text-on-dark border-dark'                                                => in_array($instr, $draftInstruments),
                                'bg-white text-subtle border-dark/15 hover:border-dark/40 hover:text-body' => !in_array($instr, $draftInstruments),
                            ])
                            aria-pressed="{{ in_array($instr, $draftInstruments) ? 'true' : 'false' }}"
                        >{{ $instr }}</button>
                    @endforeach
                </div>
            </section>

            {{-- Genres --}}
            <section>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold uppercase tracking-widest text-caption">
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
                                'bg-accent text-on-dark border-accent'                                            => in_array($genre, $draftGenres),
                                'bg-white text-subtle border-dark/15 hover:border-dark/40 hover:text-body' => !in_array($genre, $draftGenres),
                            ])
                            aria-pressed="{{ in_array($genre, $draftGenres) ? 'true' : 'false' }}"
                        >{{ $genre }}</button>
                    @endforeach
                </div>
            </section>

        </div>

        {{-- Footer --}}
        <div class="shrink-0 flex items-center gap-3 px-6 py-4 border-t border-dark/10 bg-bg">
            @if ($draftCityId !== null || $draftRadius > 0 || count($draftInstruments) > 0 || count($draftGenres) > 0 || count($draftTypes) > 0 || $draftFollowing)
                <button
                    wire:click="clear"
                    type="button"
                    class="h-11 px-5 rounded-md border border-dark/20 text-sm font-medium text-subtle hover:text-body hover:border-dark/40 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
                >
                    {{ __('explore.filter_clear') }}
                </button>
            @endif
            <button
                wire:click="apply"
                type="button"
                class="flex-1 h-11 rounded-md bg-dark text-on-dark text-sm font-medium hover:opacity-80 transition-opacity duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent-on-dark"
            >
                {{ __('explore.filter_apply') }}
            </button>
        </div>
    </div>
</div>
