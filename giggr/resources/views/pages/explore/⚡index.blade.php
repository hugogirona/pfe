<?php

use App\Models\Announcement;
use App\Models\Profile;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] #[Title('Explorer — Giggr.')] class extends Component
{
    use WithPagination;

    public string $filterCity        = '';
    public array  $filterInstruments = [];
    public array  $filterGenres      = [];

    #[Computed]
    public function filteredMusicians(): LengthAwarePaginator
    {
        return Profile::query()
            ->with(['user', 'city', 'instruments', 'genres'])
            ->when($this->filterCity, fn ($q) => $q->whereHas(
                'city', fn ($q2) => $q2->where('name', 'like', '%' . $this->filterCity . '%')
            ))
            ->when($this->filterInstruments, fn ($q) => $q->whereHas(
                'instruments', fn ($q2) => $q2->whereIn('name', $this->filterInstruments)
            ))
            ->when($this->filterGenres, fn ($q) => $q->whereHas(
                'genres', fn ($q2) => $q2->whereIn('name', $this->filterGenres)
            ))
            ->orderBy('profiles.id')
            ->paginate(12, pageName: 'musicians-page');
    }

    #[Computed]
    public function filteredAnnouncements(): LengthAwarePaginator
    {
        return Announcement::query()
            ->with(['city', 'instruments', 'genres'])
            ->active()
            ->when($this->filterCity, fn ($q) => $q->whereHas(
                'city', fn ($q2) => $q2->where('name', 'like', '%' . $this->filterCity . '%')
            ))
            ->when($this->filterInstruments, fn ($q) => $q->whereHas(
                'instruments', fn ($q2) => $q2->whereIn('name', $this->filterInstruments)
            ))
            ->when($this->filterGenres, fn ($q) => $q->whereHas(
                'genres', fn ($q2) => $q2->whereIn('name', $this->filterGenres)
            ))
            ->orderBy('announcements.id')
            ->paginate(12, pageName: 'announcements-page');
    }

    #[Computed]
    public function activeFiltersCount(): int
    {
        return count($this->filterInstruments) + count($this->filterGenres) + ($this->filterCity ? 1 : 0);
    }

    public function openFilterDrawer(): void
    {
        $this->dispatch('open-filter-drawer',
            city:        $this->filterCity,
            instruments: $this->filterInstruments,
            genres:      $this->filterGenres,
        );
    }

    #[On('filters-applied')]
    public function applyFilters(string $city, array $instruments, array $genres): void
    {
        $this->filterCity        = $city;
        $this->filterInstruments = $instruments;
        $this->filterGenres      = $genres;
        $this->resetPage('musicians-page');
        $this->resetPage('announcements-page');
    }
};
?>

<div x-data="explorerTabs">

    <x-page-header :title="__('explore.title')" :subtitle="__('explore.subtitle')" />

    <livewire:parts.explore.filter-drawer />

    <div class="max-w-6xl mx-auto px-6 py-8 space-y-6">

        <x-parts.explore.actions
            :musicians-count="$this->filteredMusicians->total()"
            :announcements-count="$this->filteredAnnouncements->total()"
            :active-filters-count="$this->activeFiltersCount"
        />

        <section
            x-show="activeTab === 'musiciens'"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
        >
            <h2 class="sr-only">{{ __('explore.tab_musicians') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($this->filteredMusicians as $profile)
                    <x-musician-card :profile="$profile" />
                @endforeach
            </div>
            @if ($this->filteredMusicians->isEmpty())
                <x-parts.explore.empty-state />
            @else
                <div class="mt-8">
                    {{ $this->filteredMusicians->links() }}
                </div>
            @endif
        </section>

        <section
            x-show="activeTab === 'annonces'"
            style="display:none"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
        >
            <h2 class="sr-only">{{ __('explore.tab_announcements') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($this->filteredAnnouncements as $announcement)
                    <x-parts.explore.announcement-card :announcement="$announcement" />
                @endforeach
            </div>
            @if ($this->filteredAnnouncements->isEmpty())
                <x-parts.explore.empty-state />
            @else
                <div class="mt-8">
                    {{ $this->filteredAnnouncements->links() }}
                </div>
            @endif
        </section>

    </div>

</div>