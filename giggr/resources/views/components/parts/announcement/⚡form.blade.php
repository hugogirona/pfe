<?php

use App\Enums\AnnouncementStatus;
use App\Enums\AnnouncementType;
use App\Models\Announcement;
use App\Models\Genre;
use App\Models\Instrument;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public ?string $model_id = null;

    #[Validate('required|min:5|max:100')]
    public string $title = '';

    #[Validate('required|in:search,formation,session,course,event')]
    public string $type = '';

    #[Validate('required|exists:cities,id')]
    public ?int $city_id = null;

    public array $selectedInstruments = [];
    public array $selectedGenres = [];

    #[Validate('required|min:20|max:1000')]
    public string $description = '';

    public bool $success = false;

    public array $availableInstruments = [];
    public array $availableGenres = [];
    public array $availableTypes = [];

    public function mount(?string $model_id = null): void
    {
        $this->model_id = $model_id;
        $this->availableInstruments = Instrument::orderBy('name')->get(['id', 'name'])->toArray();
        $this->availableGenres = Genre::orderBy('name')->get(['id', 'name'])->toArray();
        $this->availableTypes = collect(AnnouncementType::cases())
            ->map(fn($case) => ['value' => $case->value, 'label' => __($case->label())])
            ->toArray();
    }

    public function toggleInstrument(int $id): void
    {
        $this->selectedInstruments = in_array($id, $this->selectedInstruments)
            ? array_values(array_filter($this->selectedInstruments, fn($i) => $i !== $id))
            : [...$this->selectedInstruments, $id];
    }

    public function toggleGenre(int $id): void
    {
        $this->selectedGenres = in_array($id, $this->selectedGenres)
            ? array_values(array_filter($this->selectedGenres, fn($g) => $g !== $id))
            : [...$this->selectedGenres, $id];
    }

    public function save(): void
    {
        abort_unless(auth()->check(), 403);

        $this->validate();

        $announcement = Announcement::create([
            'user_id'     => auth()->id(),
            'city_id'     => $this->city_id,
            'title'       => $this->title,
            'description' => $this->description,
            'type'        => $this->type,
            'status'      => AnnouncementStatus::Open,
        ]);

        if ($this->selectedInstruments) {
            $announcement->instruments()->sync($this->selectedInstruments);
        }

        if ($this->selectedGenres) {
            $announcement->genres()->sync($this->selectedGenres);
        }

        $this->dispatch('announcement-created', id: $announcement->id);
        $this->success = true;
    }

    public function close(): void
    {
        $this->dispatch('close-modal');
    }
};
?>

<div>
@if ($success)
    <div class="flex flex-col items-center gap-4 py-4 text-center">
        <div class="w-14 h-14 rounded-full bg-pastel-salmon flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7 text-accent" aria-hidden="true">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
        </div>
        <div>
            <h3 class="font-heading text-xl text-dark">Annonce publiée !</h3>
            <p class="text-sm text-dark/60 mt-1">Votre annonce est maintenant visible par la communauté.</p>
        </div>
        <button
            wire:click="close"
            type="button"
            class="h-11 px-6 rounded-[6px] bg-dark text-bg text-sm font-medium hover:opacity-80 transition-opacity duration-150 cursor-pointer"
        >
            Fermer
        </button>
    </div>
@else
    <form wire:submit="save" class="space-y-5" novalidate>

        {{-- Titre --}}
        <div>
            <x-form.input
                name="title"
                label="Titre de l'annonce"
                wire:model.live.blur="title"
                placeholder="Ex : Cherche bassiste pour trio jazz"
                required
            />
            @error('title')
                <p class="text-xs text-accent mt-1.5" role="alert">{{ $message }}</p>
            @enderror
        </div>

        {{-- Type + Ville --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-form.select name="type" label="Type" wire:model.live.blur="type" required>
                    <option disabled selected value="">Choisir…</option>
                    @foreach ($availableTypes as $typeOption)
                        <option value="{{ $typeOption['value'] }}">{{ $typeOption['label'] }}</option>
                    @endforeach
                </x-form.select>
                @error('type')
                    <p class="text-xs text-accent mt-1.5" role="alert">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <livewire:parts.form.locality-picker wire:model.live="city_id" />
                @error('city_id')
                    <p class="text-xs text-accent mt-1.5" role="alert">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Instruments --}}
        <div class="flex flex-col gap-2">
            <span class="text-sm font-medium text-dark/70">Instruments</span>
            <div class="flex flex-wrap gap-2" role="group" aria-label="Instruments">
                @foreach ($availableInstruments as $instr)
                    <button
                        type="button"
                        wire:click="toggleInstrument({{ $instr['id'] }})"
                        @class([
                            'h-9 px-4 rounded-full border text-sm font-medium transition-all duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent',
                            'bg-dark text-bg border-dark'                                                => in_array($instr['id'], $selectedInstruments),
                            'bg-white text-dark/60 border-dark/15 hover:border-dark/40 hover:text-dark' => !in_array($instr['id'], $selectedInstruments),
                        ])
                        aria-pressed="{{ in_array($instr['id'], $selectedInstruments) ? 'true' : 'false' }}"
                    >{{ $instr['name'] }}</button>
                @endforeach
            </div>
        </div>

        {{-- Genres --}}
        <div class="flex flex-col gap-2">
            <span class="text-sm font-medium text-dark/70">Genres</span>
            <div class="flex flex-wrap gap-2" role="group" aria-label="Genres">
                @foreach ($availableGenres as $genre)
                    <button
                        type="button"
                        wire:click="toggleGenre({{ $genre['id'] }})"
                        @class([
                            'h-9 px-4 rounded-full border text-sm font-medium transition-all duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent',
                            'bg-accent text-bg border-accent'                                            => in_array($genre['id'], $selectedGenres),
                            'bg-white text-dark/60 border-dark/15 hover:border-dark/40 hover:text-dark' => !in_array($genre['id'], $selectedGenres),
                        ])
                        aria-pressed="{{ in_array($genre['id'], $selectedGenres) ? 'true' : 'false' }}"
                    >{{ $genre['name'] }}</button>
                @endforeach
            </div>
        </div>

        {{-- Description --}}
        <div>
            <x-form.textarea
                name="description"
                label="Description"
                wire:model.live.blur="description"
                placeholder="Décrivez votre projet, votre niveau, vos disponibilités…"
                :rows="4"
                required
            />
            @error('description')
                <p class="text-xs text-accent mt-1.5" role="alert">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <div class="flex justify-end pt-2 border-t border-dark/10">
            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-not-allowed"
                class="h-11 px-6 rounded-[6px] bg-dark text-bg text-sm font-medium hover:opacity-80 transition-opacity duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
            >
                <span wire:loading.remove wire:target="save">Publier l'annonce</span>
                <span wire:loading wire:target="save">Publication…</span>
            </button>
        </div>

    </form>
@endif
</div>
