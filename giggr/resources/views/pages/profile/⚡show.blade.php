<?php

use App\Enums\ProfileStatus;
use App\Models\Genre;
use App\Models\Instrument;
use App\Models\Profile;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

new #[Layout('layouts.app')]
class extends Component
{
    public Profile $profile;

    public bool $isOwner = false;

    public string $bio = '';

    public string $selectedStatus = '';

    public array $selectedInstruments = [];

    public array $selectedGenres = [];

    public array $allStatuses = [];

    public array $allInstruments = [];

    public array $allGenres = [];

    public function hydrate(): void
    {
        $this->reloadRelationCounts();
    }

    public function mount(Profile $profile): void
    {
        // $profile is resolved from the "name-slug-{id}" route key by
        // Profile::resolveRouteBinding(); here we just load what the view needs.
        $this->profile = $profile->load([
            'user',
            'city',
            'instruments',
            'genres',
            'media',
            'user.announcements' => fn ($q) => $q->active()->with(['city', 'instruments', 'genres']),
        ])->loadCount([
            'followers',
            'followed as followed_count',
        ]);

        $this->isOwner = auth()->user()?->can('update', $this->profile) ?? false;

        if ($this->isOwner) {
            $this->bio = $this->profile->bio ?? '';
            $this->selectedStatus = $this->profile->status->value;
            $this->selectedInstruments = $this->profile->instruments->pluck('id')->toArray();
            $this->selectedGenres = $this->profile->genres->pluck('id')->toArray();
            $this->allStatuses = collect(ProfileStatus::selectable())
                ->map(fn ($case) => ['value' => $case->value, 'label' => __($case->label())])
                ->all();
            $this->allInstruments = Instrument::orderBy('name')->get()
                ->mapWithKeys(fn (Instrument $i) => [$i->id => $i->translated_name])
                ->all();
            $this->allGenres = Genre::orderBy('name')->get()
                ->mapWithKeys(fn (Genre $g) => [$g->id => $g->translated_name])
                ->all();
        }
    }

    public function render(): View
    {
        $description = filled($this->profile->bio)
            ? Str::limit(strip_tags($this->profile->bio), 155)
            : __('seo.descriptions.profile', ['name' => $this->profile->user->full_name]);

        return $this->view()
            ->title($this->profile->user->full_name)
            ->layout('layouts.app', ['description' => $description]);
    }

    public function saveBio(): void
    {
        $this->authorize('update', $this->profile);
        $this->validate(['bio' => ['required', 'string', 'min:10', 'max:1000']]);
        $this->profile->update(['bio' => $this->bio]);
        $this->dispatch('bio-saved');
    }

    public function saveStatus(?string $value = null): void
    {
        $this->authorize('update', $this->profile);
        if ($value !== null) {
            $this->selectedStatus = $value;
        }
        $this->validate([
            'selectedStatus' => ['required', 'in:'.implode(',', array_column(ProfileStatus::selectable(), 'value'))],
        ]);
        $this->profile->update(['status' => $this->selectedStatus]);
        $this->dispatch('status-saved');
    }

    public function toggleInstrument(int $id): void
    {
        $this->authorize('update', $this->profile);
        $this->selectedInstruments = in_array($id, $this->selectedInstruments)
            ? array_values(array_filter($this->selectedInstruments, fn ($i) => $i !== $id))
            : [...$this->selectedInstruments, $id];
    }

    public function saveInstruments(): void
    {
        $this->authorize('update', $this->profile);
        $this->validate([
            'selectedInstruments' => ['array'],
            'selectedInstruments.*' => ['integer', 'exists:instruments,id'],
        ]);
        $this->profile->instruments()->sync($this->selectedInstruments);
        $this->profile->load('instruments');
        $this->dispatch('instruments-saved');
    }

    public function toggleGenre(int $id): void
    {
        $this->authorize('update', $this->profile);
        $this->selectedGenres = in_array($id, $this->selectedGenres)
            ? array_values(array_filter($this->selectedGenres, fn ($g) => $g !== $id))
            : [...$this->selectedGenres, $id];
    }

    public function saveGenres(): void
    {
        $this->authorize('update', $this->profile);
        $this->validate([
            'selectedGenres' => ['array'],
            'selectedGenres.*' => ['integer', 'exists:genres,id'],
        ]);
        $this->profile->genres()->sync($this->selectedGenres);
        $this->profile->load('genres');
        $this->dispatch('genres-saved');
    }

    /**
     * Avatar and media processing run in queued jobs that broadcast back on the
     * owner's private user channel. Only the owner sees these live updates, so we
     * subscribe conditionally: a visitor would otherwise request authorization for
     * someone else's private channel on /broadcasting/auth and receive a 403.
     *
     * @return array<string, string>
     */
    public function getListeners(): array
    {
        if (! $this->isOwner) {
            return [];
        }

        $userId = (int) $this->profile->user_id;

        return [
            "echo-private:App.Models.User.{$userId},.avatar.processed" => 'refreshAvatar',
            "echo-private:App.Models.User.{$userId},.media.processed" => 'refreshMedia',
        ];
    }

    public function refreshAvatar(): void
    {
        $this->profile->refresh();
        $this->reloadRelationCounts();
    }

    #[On('announcement-created')]
    #[On('announcement-updated')]
    #[On('announcement-deleted')]
    public function refreshAnnouncements(): void
    {
        $this->profile->load([
            'user.announcements' => fn ($q) => $q->active()->with(['city', 'instruments', 'genres']),
        ]);
    }

    #[On('media-added')]
    #[On('media-updated')]
    #[On('media-deleted')]
    public function refreshMedia(): void
    {
        $this->profile->load('media');
    }

    #[On('follow-state-changed')]
    public function refreshCounts(): void
    {
        $this->reloadRelationCounts();
    }

    #[On('echo:profile.{profile.id},.contact-preference.updated')]
    public function refreshContactState(): void
    {
        $this->profile->user->unsetRelation('profile');
    }

    private function reloadRelationCounts(): void
    {
        $this->profile->loadCount([
            'followers',
            'followed as followed_count',
        ]);
    }
};
?>

<div>

    <livewire:parts.social.relations-modal/>
    <livewire:parts.profile.media-lightbox/>

    <x-parts.profile.hero/>

    <div class="max-w-6xl mx-auto px-6 py-10">
        <div class="flex flex-col lg:flex-row gap-8 lg:items-start" itemscope itemtype="https://schema.org/Person">
            <link itemprop="url" href="{{ route('profile', $profile) }}">
            @foreach ($profile->instruments as $instrument)
                <meta itemprop="knowsAbout" content="{{ $instrument->translated_name }}">
            @endforeach
            @foreach ($profile->genres as $genre)
                <meta itemprop="knowsAbout" content="{{ $genre->translated_name }}">
            @endforeach

            {{-- Sidebar --}}
            <aside class="w-full lg:w-80 shrink-0 lg:sticky lg:top-24" aria-label="{{ $profile->user->full_name }}">
                <h2 class="sr-only">{{ __('profile.card_heading', ['name' => $profile->user->full_name]) }}</h2>
                <x-parts.profile.identity-card
                    :profile="$profile"
                    :isOwner="$isOwner"
                    :allStatuses="$allStatuses"
                    :selectedStatus="$selectedStatus"
                    :allInstruments="$allInstruments"
                    :allGenres="$allGenres"
                    :selectedInstruments="$selectedInstruments"
                    :selectedGenres="$selectedGenres"
                    :followers-count="$profile->followers_count ?? 0"
                    :followed-count="$profile->followed_count ?? 0"
                />
            </aside>

            {{-- Main content --}}
            <div class="w-full lg:flex-1 min-w-0 space-y-6">
                <x-parts.profile.about :profile="$profile" :isOwner="$isOwner"/>
                <x-parts.profile.media-gallery :profile="$profile" :isOwner="$isOwner"/>
                <x-parts.profile.announcements :profile="$profile" :isOwner="$isOwner" />
            </div>

        </div>
    </div>

</div>
