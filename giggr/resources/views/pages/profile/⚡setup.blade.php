<?php

use App\Enums\ProfileStatus;
use App\Models\City;
use App\Models\Genre;
use App\Models\Instrument;
use App\Models\Profile;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')]
#[Title('Complète ton profil — Giggr.')]
class extends Component {
    public string $bio = '';
    public ?string $cityId = null;
    public int $experienceYears = 0;
    public string $status = '';

    public array $selectedInstruments = [];
    public array $selectedGenres = [];

    public array $cities = [];
    public array $instruments = [];
    public array $genres = [];

    public function mount(): void
    {
        $profile = Profile::with(['instruments', 'genres'])
            ->firstOrCreate(['user_id' => auth()->id()]);

        if ($profile->bio && $profile->instruments->isNotEmpty()) {
            $this->redirect(route('profile', ['id' => $profile->id]), navigate: true);
            return;
        }

        $this->cities = City::orderBy('name')->pluck('name', 'id')->toArray();
        $this->instruments = Instrument::orderBy('name')->pluck('name', 'id')->toArray();
        $this->genres = Genre::orderBy('name')->pluck('name', 'id')->toArray();

        $this->bio = $profile->bio ?? '';
        $this->cityId = $profile->city_id ? (string)$profile->city_id : null;
        $this->experienceYears = $profile->experience_years;
        $this->status = $profile->status->value;

        $this->selectedInstruments = $profile->instruments->pluck('id')->toArray();
        $this->selectedGenres = $profile->genres->pluck('id')->toArray();
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
        $this->cityId = $this->cityId ?: null;

        $this->validate([
            'bio' => ['required', 'string', 'min:10', 'max:1000'],
            'cityId' => ['nullable', 'exists:cities,id'],
            'experienceYears' => ['required', 'integer', 'min:0', 'max:60'],
            'status' => ['required', 'string', 'in:' . implode(',', array_column(ProfileStatus::cases(), 'value'))],
        ]);

        $profile = auth()->user()->profile;

        $profile->update([
            'bio' => $this->bio,
            'city_id' => $this->cityId ?: null,
            'experience_years' => $this->experienceYears,
            'status' => $this->status,
        ]);

        $profile->instruments()->sync($this->selectedInstruments);
        $profile->genres()->sync($this->selectedGenres);

        $this->redirect(route('profile', ['id' => $profile->id]), navigate: true);
    }

    public function skip(): void
    {
        $this->redirect(route('explore'), navigate: true);
    }
};
?>

<div>
    <div class="relative h-52 md:h-64 bg-dark overflow-hidden" aria-hidden="false">
        <div class="absolute inset-0 opacity-[0.04]"
             style="background-image: repeating-linear-gradient(0deg, currentColor, currentColor 1px, transparent 1px, transparent 40px), repeating-linear-gradient(90deg, currentColor, currentColor 1px, transparent 1px, transparent 40px); color: white;"
             aria-hidden="true"></div>

        <div class="absolute bottom-6 left-0 right-0">
            <div class="max-w-2xl mx-auto px-6">
                <p class="text-bg/40 text-xs font-medium uppercase tracking-widest mb-1">
                    {{ __('Bienvenue sur Giggr.') }}
                </p>
                <h1 class="font-heading text-3xl md:text-4xl text-bg leading-tight">
                    {{ __('Complète ton profil,') }} {{ auth()->user()->first_name }}.
                </h1>
            </div>
        </div>

    </div>

    {{-- Form --}}
    <div class="max-w-2xl mx-auto px-6 py-10">
        <form wire:submit="save" novalidate class="space-y-6"
              aria-label="{{ __('Formulaire de configuration du profil') }}">

            {{-- ── À propos ── --}}
            <section class="bg-white rounded-2xl border border-dark/10 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-dark/[0.07]">
                    <h2 class="font-heading text-lg text-dark">{{ __('À propos') }}</h2>
                </div>
                <div class="px-6 py-5">
                    <x-form.textarea
                        name="bio"
                        :label="__('Présente-toi')"
                        :required="true"
                        :rows="4"
                        :placeholder="__('Je suis bassiste depuis 8 ans, j\'aime le jazz et le funk...')"
                        wire:model="bio"
                    />
                    @error('bio')
                    <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            {{-- ── Localisation & expérience ── --}}
            <section class="bg-white rounded-2xl border border-dark/10 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-dark/[0.07]">
                    <h2 class="font-heading text-lg text-dark">{{ __('Localisation & expérience') }}</h2>
                </div>
                <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

                    <x-form.select name="cityId" :label="__('Ville')" wire:model="cityId">
                        <option value="">{{ __('Choisir une ville') }}</option>
                        @foreach ($cities as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </x-form.select>
                    @error('cityId')
                    <p class="text-xs text-danger">{{ $message }}</p>
                    @enderror

                    <x-form.input
                        name="experienceYears"
                        type="number"
                        :label="__('Années d\'expérience')"
                        :required="true"
                        :placeholder="__('0')"
                        wire:model.number="experienceYears"
                    />
                    @error('experienceYears')
                    <p class="text-xs text-danger">{{ $message }}</p>
                    @enderror

                    <div class="sm:col-span-2">
                        <x-form.select name="status" :label="__('Disponibilité')" :required="true" wire:model="status">
                            @foreach (App\Enums\ProfileStatus::cases() as $case)
                                <option value="{{ $case->value }}">{{ __($case->label()) }}</option>
                            @endforeach
                        </x-form.select>
                        @error('status')
                        <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </section>

            {{-- ── Instruments ── --}}
            <section class="bg-white rounded-2xl border border-dark/10 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-dark/[0.07] flex items-center justify-between">
                    <h2 class="font-heading text-lg text-dark">{{ __('Instruments') }}</h2>
                    @if (count($selectedInstruments) > 0)
                        <span class="text-xs font-semibold text-accent">{{ count($selectedInstruments) }}</span>
                    @endif
                </div>
                <div class="px-6 py-5">
                    <x-parts.profile.pill-group
                        :items="$instruments"
                        :selected="$selectedInstruments"
                        method="toggleInstrument"
                        variant="dark"
                    />
                </div>
            </section>

            {{-- ── Genres ── --}}
            <section class="bg-white rounded-2xl border border-dark/10 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-dark/[0.07] flex items-center justify-between">
                    <h2 class="font-heading text-lg text-dark">{{ __('Genres musicaux') }}</h2>
                    @if (count($selectedGenres) > 0)
                        <span class="text-xs font-semibold text-accent">{{ count($selectedGenres) }}</span>
                    @endif
                </div>
                <div class="px-6 py-5">
                    <x-parts.profile.pill-group
                        :items="$genres"
                        :selected="$selectedGenres"
                        method="toggleGenre"
                        variant="accent"
                    />
                </div>
            </section>

            {{-- ── Actions ── --}}
            <div class="flex flex-col sm:flex-row items-center gap-4 pt-2">
                <x-cta type="submit" variant="dark" size="lg" class="w-full sm:w-auto">
                    {{ __('Enregistrer mon profil') }}
                </x-cta>
                <button
                    type="button"
                    wire:click="skip"
                    class="text-sm text-dark/45 hover:text-dark underline underline-offset-2 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-sm"
                >
                    {{ __('Passer pour l\'instant') }}
                </button>
            </div>

        </form>
    </div>

</div>
