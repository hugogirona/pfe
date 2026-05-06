<?php

use App\Models\City;
use Illuminate\Support\Str;
use Livewire\Attributes\Modelable;
use Livewire\Component;

new class extends Component {
    #[Modelable]
    public ?int $cityId = null;

    public string $query = '';

    /** @var array<int, array{id:int, display:string}> */
    public array $results = [];

    public function mount(): void
    {
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
        if (! $city) {
            return;
        }

        $this->cityId  = $city->id;
        $this->query   = $city->display_name;
        $this->results = [];
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
            ->where('searchable', 'like', '%'.$needle.'%')
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'postal_code'])
            ->map(fn (City $c) => ['id' => $c->id, 'display' => $c->display_name])
            ->all();
    }
};
?>

<div
    class="relative"
    x-data="{ highlight: 0 }"
    @keydown.escape.window="$wire.set('results', [])"
>
    <label for="locality-picker-input" class="text-sm font-medium text-dark/70">
        Ville<span class="text-accent ml-0.5" aria-hidden="true">*</span>
    </label>
    <input
        id="locality-picker-input"
        type="text"
        wire:model.live.debounce.200ms="query"
        placeholder="Ex : Liège, Antwerpen, 4000…"
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
        class="w-full px-4 py-3 mt-1.5 rounded-[6px] bg-white border border-dark/15 text-base text-dark placeholder:text-dark/30 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-colors duration-150"
    />

    @if (count($results) > 0)
        <ul
            id="locality-picker-list"
            role="listbox"
            class="absolute left-0 right-0 z-20 mt-1 max-h-72 overflow-y-auto rounded-[6px] bg-white border border-dark/15 shadow-lg"
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
