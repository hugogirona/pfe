<?php

use App\Models\Announcement;
use App\Models\Follow;
use App\Models\Profile;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')]
class extends Component {
    use WithPagination;

    public string $activeTab = 'profiles';

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public ?int $filterCityId = null;
    public array $filterInstruments = [];
    public array $filterGenres = [];
    public array $filterTypes = [];
    public bool $filterFollowing = false;

    public function mount(?string $tab = null): void
    {
        $this->activeTab = $tab === __('explore.tab_announcements_slug') ? 'announcements' : 'profiles';
    }

    public function updatedSearch(): void
    {
        $this->resetPage('profiles-page');
        $this->resetPage('announcements-page');
    }

    private function hasSearch(): bool
    {
        return $this->search
            |> trim(...)
            |> filled(...);
    }

    #[Computed]
    public function followedProfileIdsForFilter(): array
    {
        return auth()->check()
            ? auth()->user()->followedProfileIds()
            : [];
    }

    #[Computed]
    public function filteredProfiles(): LengthAwarePaginator
    {
        $followingActive = $this->filterFollowing && auth()->check();

        return Profile::query()
            ->with(['user', 'city', 'instruments', 'genres'])
            ->when($this->filterCityId !== null, fn($q) => $q->where('city_id', $this->filterCityId))
            ->when($this->filterInstruments, fn($q) => $q->whereHas('instruments', fn($q2) => $q2->whereIn('name', $this->filterInstruments)))
            ->when($this->filterGenres, fn($q) => $q->whereHas('genres', fn($q2) => $q2->whereIn('name', $this->filterGenres)))
            ->when($this->hasSearch(), fn($q) => $q->search($this->search))
            ->when($followingActive, fn($q) => $q->whereIn('id', $this->followedProfileIdsForFilter))
            ->orderByDesc('profiles.created_at')
            ->orderByDesc('profiles.id')
            ->paginate(12, pageName: 'profiles-page')
            ->onEachSide(1);
    }

    #[Computed]
    public function filteredAnnouncements(): LengthAwarePaginator
    {
        $followingActive = $this->filterFollowing && auth()->check();

        return Announcement::query()
            ->with(['city', 'instruments', 'genres'])
            ->active()
            ->when($this->filterCityId !== null, fn($q) => $q->where('city_id', $this->filterCityId))
            ->when($this->filterInstruments, fn($q) => $q->whereHas('instruments', fn($q2) => $q2->whereIn('name', $this->filterInstruments)))
            ->when($this->filterGenres, fn($q) => $q->whereHas('genres', fn($q2) => $q2->whereIn('name', $this->filterGenres)))
            ->when($this->filterTypes, fn($q) => $q->whereIn('type', $this->filterTypes))
            ->when($this->hasSearch(), fn($q) => $q->search($this->search))
            ->when($followingActive, fn($q) => $q->whereHas(
                'user.profile', fn($q2) => $q2->whereIn('id', $this->followedProfileIdsForFilter)
            ))
            ->orderByDesc('announcements.created_at')
            ->orderByDesc('announcements.id')
            ->paginate(12, pageName: 'announcements-page')
            ->onEachSide(1);
    }

    #[Computed]
    public function followedProfileIds(): array
    {
        $viewer = auth()->user();
        if ($viewer === null) {
            return [];
        }

        $profileIds = $this->filteredProfiles->pluck('id')->all();
        if ($profileIds === []) {
            return [];
        }

        return Follow::where('user_id', $viewer->id)
            ->where('followable_type', 'profile')
            ->whereIn('followable_id', $profileIds)
            ->pluck('followable_id')
            ->all();
    }

    #[Computed]
    public function activeFiltersCount(): int
    {
        return count($this->filterInstruments)
            + count($this->filterGenres)
            + count($this->filterTypes)
            + ($this->filterCityId !== null ? 1 : 0)
            + ($this->filterFollowing ? 1 : 0);
    }

    public function openFilterDrawer(): void
    {
        $this->dispatch('open-filter-drawer',
            cityId: $this->filterCityId,
            instruments: $this->filterInstruments,
            genres: $this->filterGenres,
            types: $this->filterTypes,
            following: $this->filterFollowing,
            activeTab: $this->activeTab,
        );
    }

    #[On('filters-applied')]
    public function applyFilters(?int $cityId, array $instruments, array $genres, array $types, bool $following = false): void
    {
        $this->filterCityId = $cityId;
        $this->filterInstruments = $instruments;
        $this->filterGenres = $genres;
        $this->filterTypes = $types;
        $this->filterFollowing = $following;
        $this->resetPage('profiles-page');
        $this->resetPage('announcements-page');
    }

    #[On('announcement-created')]
    #[On('announcement-updated')]
    #[On('announcement-deleted')]
    public function refreshAnnouncements(): void
    {
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return $this->view()->title(__('titles.explore'));
    }
};
?>

<div>

    <x-page-header :title="__('explore.title')" :subtitle="__('explore.subtitle')"/>

    <livewire:parts.explore.filter-drawer/>

    <div class="max-w-6xl mx-auto px-6 py-8 space-y-6">

        <x-search-bar
            wire:model.live.debounce.300ms="search"
            name="explore-search"
            :placeholder="__('explore.search_placeholder')"
            :label="__('explore.search_label')"
        />

        <x-parts.explore.actions
            :active-tab="$activeTab"
            :profiles-count="$this->filteredProfiles->total()"
            :announcements-count="$this->filteredAnnouncements->total()"
            :active-filters-count="$this->activeFiltersCount"
        />

        @if ($activeTab === 'profiles')
            <section>
                <h2 class="sr-only">{{ __('explore.tab_profiles') }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 items-start">
                    @foreach ($this->filteredProfiles as $profile)
                        <x-profile-card :wire:key="'profile-'.$profile->id" :profile="$profile" :followed-profile-ids="$this->followedProfileIds"/>
                    @endforeach
                </div>
                @if ($this->filteredProfiles->isEmpty())
                    <x-parts.explore.empty-state/>
                @else
                    <div class="mt-8">
                        {{ $this->filteredProfiles->links() }}
                    </div>
                @endif
            </section>
        @else
            <section>
                <h2 class="sr-only">{{ __('explore.tab_announcements') }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($this->filteredAnnouncements as $announcement)
                        <x-parts.explore.announcement-card :wire:key="'announcement-'.$announcement->id" :announcement="$announcement"/>
                    @endforeach
                </div>
                @if ($this->filteredAnnouncements->isEmpty())
                    <x-parts.explore.empty-state/>
                @else
                    <div class="mt-8">
                        {{ $this->filteredAnnouncements->links() }}
                    </div>
                @endif
            </section>
        @endif

    </div>

</div>
