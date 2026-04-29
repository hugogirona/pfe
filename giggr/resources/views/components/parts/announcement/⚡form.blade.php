<?php

use Livewire\Component;

new class extends Component {
    public ?string $model_id = null;

    public string $title = '';
    public string $type = '';
    public string $city = '';
    public array $selectedInstruments = [];
    public array $selectedGenres = [];
    public string $description = '';
    public bool $success = false;

    public array $availableInstruments = ['Guitare', 'Basse', 'Batterie', 'Clavier', 'Violon', 'Chant', 'Saxophone', 'Trompette', 'Percussions'];
    public array $availableGenres = ['Rock', 'Jazz', 'Pop', 'Folk', 'Metal', 'Classique', 'Electronic', 'Soul', 'Indie', 'Blues', 'World', 'Funk'];
    public array $availableTypes = ['Recherche', 'Formation', 'Événement', 'Session', 'Cours'];

    public function toggleInstrument(string $instrument): void
    {
        $this->selectedInstruments = in_array($instrument, $this->selectedInstruments)
            ? array_values(array_filter($this->selectedInstruments, fn($i) => $i !== $instrument))
            : [...$this->selectedInstruments, $instrument];
    }

    public function toggleGenre(string $genre): void
    {
        $this->selectedGenres = in_array($genre, $this->selectedGenres)
            ? array_values(array_filter($this->selectedGenres, fn($g) => $g !== $genre))
            : [...$this->selectedGenres, $genre];
    }

    public function save(): void
    {
        $this->validate([
            'title'       => 'required|min:5|max:100',
            'type'        => 'required',
            'city'        => 'required',
            'description' => 'required|min:20|max:1000',
        ]);

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
                wire:model="title"
                placeholder="Ex : Cherche bassiste pour trio jazz"
                required
            />
            @error('title')
                <p class="text-xs text-accent mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Type + Ville --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-form.select name="type" label="Type" wire:model="type" required>
                    <option value="">Choisir…</option>
                    @foreach ($availableTypes as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </x-form.select>
                @error('type')
                    <p class="text-xs text-accent mt-1.5">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <x-form.input
                    name="city"
                    label="Ville"
                    wire:model="city"
                    placeholder="Ex : Liège"
                    required
                />
                @error('city')
                    <p class="text-xs text-accent mt-1.5">{{ $message }}</p>
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
                        wire:click="toggleInstrument('{{ $instr }}')"
                        @class([
                            'h-9 px-4 rounded-full border text-sm font-medium transition-all duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent',
                            'bg-dark text-bg border-dark'                                                   => in_array($instr, $selectedInstruments),
                            'bg-white text-dark/60 border-dark/15 hover:border-dark/40 hover:text-dark'    => !in_array($instr, $selectedInstruments),
                        ])
                        aria-pressed="{{ in_array($instr, $selectedInstruments) ? 'true' : 'false' }}"
                    >{{ $instr }}</button>
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
                        wire:click="toggleGenre('{{ $genre }}')"
                        @class([
                            'h-9 px-4 rounded-full border text-sm font-medium transition-all duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent',
                            'bg-accent text-bg border-accent'                                               => in_array($genre, $selectedGenres),
                            'bg-white text-dark/60 border-dark/15 hover:border-dark/40 hover:text-dark'    => !in_array($genre, $selectedGenres),
                        ])
                        aria-pressed="{{ in_array($genre, $selectedGenres) ? 'true' : 'false' }}"
                    >{{ $genre }}</button>
                @endforeach
            </div>
        </div>

        {{-- Description --}}
        <div>
            <x-form.textarea
                name="description"
                label="Description"
                wire:model="description"
                placeholder="Décrivez votre projet, votre niveau, vos disponibilités…"
                :rows="4"
                required
            />
            @error('description')
                <p class="text-xs text-accent mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <div class="flex justify-end pt-2 border-t border-dark/10">
            <button
                type="submit"
                class="h-11 px-6 rounded-[6px] bg-dark text-bg text-sm font-medium hover:opacity-80 transition-opacity duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
            >
                Publier l'annonce
            </button>
        </div>

    </form>
@endif
</div>
