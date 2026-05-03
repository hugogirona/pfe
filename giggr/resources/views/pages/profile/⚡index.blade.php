<?php

use App\Models\Genre;
use App\Models\Instrument;
use App\Models\Profile;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Profil — Giggr.')] class extends Component
{
    public Profile $profile;
    public bool $isOwner = false;

    public string $bio = '';
    public array $selectedInstruments = [];
    public array $selectedGenres = [];
    public array $allInstruments = [];
    public array $allGenres = [];

    public function mount(int $id): void
    {
        $this->profile = Profile::with([
            'user',
            'city',
            'instruments',
            'genres',
            'user.announcements' => fn ($q) => $q->active()->with(['city', 'instruments', 'genres']),
        ])->findOrFail($id);

        $this->isOwner = auth()->check() && auth()->id() === $this->profile->user_id;

        if ($this->isOwner) {
            $this->bio = $this->profile->bio ?? '';
            $this->selectedInstruments = $this->profile->instruments->pluck('id')->toArray();
            $this->selectedGenres = $this->profile->genres->pluck('id')->toArray();
            $this->allInstruments = Instrument::orderBy('name')->pluck('name', 'id')->toArray();
            $this->allGenres = Genre::orderBy('name')->pluck('name', 'id')->toArray();
        }
    }

    public function saveBio(): void
    {
        abort_unless($this->isOwner, 403);
        $this->validate(['bio' => ['required', 'string', 'min:10', 'max:1000']]);
        $this->profile->update(['bio' => $this->bio]);
        $this->dispatch('bio-saved');
    }

    public function toggleInstrument(int $id): void
    {
        abort_unless($this->isOwner, 403);
        $this->selectedInstruments = in_array($id, $this->selectedInstruments)
            ? array_values(array_filter($this->selectedInstruments, fn ($i) => $i !== $id))
            : [...$this->selectedInstruments, $id];
    }

    public function saveInstruments(): void
    {
        abort_unless($this->isOwner, 403);
        $this->profile->instruments()->sync($this->selectedInstruments);
        $this->profile->load('instruments');
        $this->dispatch('instruments-saved');
    }

    public function toggleGenre(int $id): void
    {
        abort_unless($this->isOwner, 403);
        $this->selectedGenres = in_array($id, $this->selectedGenres)
            ? array_values(array_filter($this->selectedGenres, fn ($g) => $g !== $id))
            : [...$this->selectedGenres, $id];
    }

    public function saveGenres(): void
    {
        abort_unless($this->isOwner, 403);
        $this->profile->genres()->sync($this->selectedGenres);
        $this->profile->load('genres');
        $this->dispatch('genres-saved');
    }

    #[On('announcement-created')]
    public function refreshAnnouncements(): void
    {
        $this->profile->load([
            'user.announcements' => fn ($q) => $q->active()->with(['city', 'instruments', 'genres']),
        ]);
    }
};
?>

<div>

    <x-parts.profile.hero :profile="$profile" />

    <div class="max-w-6xl mx-auto px-6 py-10">
        <div class="flex flex-col lg:flex-row gap-8 lg:items-start">

            {{-- Sidebar --}}
            <aside class="w-full lg:w-80 shrink-0 lg:sticky lg:top-24" aria-label="{{ $profile->user->full_name }}">
                <x-parts.profile.identity-card
                    :profile="$profile"
                    :isOwner="$isOwner"
                    :allInstruments="$allInstruments"
                    :allGenres="$allGenres"
                    :selectedInstruments="$selectedInstruments"
                    :selectedGenres="$selectedGenres"
                />
            </aside>

            {{-- Main content --}}
            <div class="w-full lg:flex-1 min-w-0 space-y-6">
                <x-parts.profile.about :profile="$profile" :isOwner="$isOwner" />
                <x-parts.profile.media-gallery :profile="$profile" :isOwner="$isOwner" />
                <x-parts.profile.announcements :profile="$profile" :isOwner="$isOwner" />
            </div>

        </div>
    </div>

</div>
