<?php

use App\Models\City;
use Illuminate\Support\Str;
use Livewire\Attributes\Modelable;
use Livewire\Component;

new class extends Component {
    private const int GEOLOC_MAX_RADIUS_KM = 50;

    #[Modelable]
    public ?int $cityId = null;

    public string $label = '';

    public string $placeholder = '';

    public bool $required = true;

    public bool $filterStyle = false;

    public string $query = '';

    public bool $tooFar = false;

    /** @var array<int, array{id:int, display:string}> */
    public array $results = [];

    public function mount(?string $label = null, ?string $placeholder = null, bool $filterStyle = false): void
    {
        $this->label = $label ?? __('announcement.form_city_label');
        $this->placeholder = $placeholder ?? __('announcement.form_city_placeholder');
        $this->filterStyle = $filterStyle;

        if ($this->cityId !== null) {
            $city = City::find($this->cityId);
            if ($city) {
                $this->query = $city->display_name;
            }
        }
    }

    public function updatedQuery(): void
    {
        $this->cityId = null;
        $this->loadResults();
    }

    public function selectCity(int $id): void
    {
        $city = City::find($id);
        if (!$city) {
            return;
        }

        $this->cityId = $city->id;
        $this->query = $city->display_name;
        $this->results = [];
        $this->tooFar = false;
    }

    public function selectFromCoords(float $lat, float $lng): void
    {
        $nearest = City::query()
            ->nearby($lat, $lng, self::GEOLOC_MAX_RADIUS_KM)
            ->orderByDistance($lat, $lng)
            ->first();

        if ($nearest === null) {
            $this->tooFar = true;

            return;
        }

        $this->cityId = $nearest->id;
        $this->query = $nearest->display_name;
        $this->results = [];
        $this->tooFar = false;
    }

    private function loadResults(): void
    {
        $term = trim($this->query);
        if ($term === '') {
            $this->results = [];

            return;
        }

        $needle = Str::slug($term);
        if ($needle === '') {
            $needle = $term;
        }

        $this->results = City::query()
            ->where('searchable', 'like', '%' . $needle . '%')
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'postal_code'])
            ->map(fn(City $c) => ['id' => $c->id, 'display' => $c->display_name])
            ->all();
    }
};
?>

<div
    class="relative"
    x-data="{
        highlight: 0,
        locating: false,
        geoError: null,
        async geolocate() {
            if (! navigator.geolocation) {
                this.geoError = @js(__('locality.geoloc_unavailable'));
                return;
            }
            this.geoError = null;
            this.locating = true;
            navigator.geolocation.getCurrentPosition(
                async (pos) => {
                    try {
                        await $wire.selectFromCoords(pos.coords.latitude, pos.coords.longitude);
                    } finally {
                        this.locating = false;
                    }
                },
                (err) => {
                    this.locating = false;
                    if (err.code === 1) this.geoError = @js(__('locality.geoloc_permission_denied'));
                    else if (err.code === 2) this.geoError = @js(__('locality.geoloc_unavailable'));
                    else this.geoError = @js(__('locality.geoloc_timeout'));
                },
                { enableHighAccuracy: false, timeout: 8000, maximumAge: 60000 },
            );
        },
    }"
    @keydown.escape.window="$wire.set('results', [])"
>
    <label for="locality-picker-input" @class([
        'block text-xs font-semibold uppercase tracking-widest text-dark/40 mb-3' => $filterStyle,
        'block text-sm font-medium text-dark/70 mb-1.5' => !$filterStyle,
    ])>
        {{ $label }}
        @if ($required && !$filterStyle)
            <span class="text-accent ml-0.5" aria-hidden="true">*</span>
        @endif
    </label>

    <div class="relative">
        <input
            id="locality-picker-input"
            type="text"
            wire:model.live.debounce.200ms="query"
            placeholder="{{ $placeholder }}"
            autocomplete="off"
            role="combobox"
            aria-expanded="{{ count($results) > 0 ? 'true' : 'false' }}"
            aria-controls="locality-picker-list"
            @keydown.arrow-down.prevent="highlight = Math.min(highlight + 1, {{ max(count($results) - 1, 0) }})"
            @keydown.arrow-up.prevent="highlight = Math.max(highlight - 1, 0)"
            @keydown.enter.prevent="
                if ($wire.results[highlight]) {
                    $wire.call('selectCity', $wire.results[highlight].id)
                    highlight = 0
                }
            "
            class="w-full px-4 py-3 pr-12 rounded-md bg-white border border-dark/15 text-base text-dark placeholder:text-dark/30 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-colors duration-150"
        />

        <button
            type="button"
            @click="geolocate"
            :disabled="locating"
            :aria-busy="locating"
            aria-label="{{ __('locality.geolocate_aria') }}"
            class="absolute inset-y-0 right-2 my-auto w-9 h-9 flex items-center justify-center rounded-md text-dark/50 hover:text-accent hover:bg-dark/5 transition-colors duration-150 cursor-pointer disabled:cursor-not-allowed focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
        >
            <span x-show="!locating" class="inline-flex motion-reduce:!animate-none">
                <x-icon name="map-pin" class="w-5 h-5"/>
            </span>
            <span
                x-show="locating"
                x-cloak
                class="inline-block w-4 h-4 border-2 border-dark/20 border-t-accent rounded-full animate-spin motion-reduce:animate-none"
                role="status"
                aria-label="{{ __('locality.geolocating') }}"
            ></span>
        </button>
    </div>

    <p
        x-show="geoError"
        x-cloak
        x-text="geoError"
        role="alert"
        class="text-xs text-danger mt-1.5"
    ></p>

    @if ($tooFar)
        <p role="alert" class="text-xs text-danger mt-1.5">
            {{ __('locality.geoloc_too_far') }}
        </p>
    @endif

    @if (count($results) > 0)
        <ul
            id="locality-picker-list"
            role="listbox"
            class="absolute left-0 right-0 z-20 mt-1 max-h-72 overflow-y-auto rounded-md bg-white border border-dark/15 shadow-lg"
        >
            @foreach ($results as $i => $r)
                <li
                    role="option"
                    :aria-selected="highlight === {{ $i }}"
                    @click="$wire.call('selectCity', {{ $r['id'] }})"
                    @mouseenter="highlight = {{ $i }}"
                    :class="highlight === {{ $i }} ? 'bg-pastel-salmon text-dark' : 'text-dark/80'"
                    class="px-4 py-2.5 text-sm cursor-pointer hover:bg-pastel-salmon/60"
                >{{ $r['display'] }}</li>
            @endforeach
        </ul>
    @endif
</div>
