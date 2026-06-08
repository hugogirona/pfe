<?php

use App\Models\Announcement;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

new #[Layout('layouts.app')] class extends Component
{
    public Announcement $announcement;

    public Collection $related;

    public function mount(int $id): void
    {
        $this->announcement = Announcement::with([
            'city',
            'instruments',
            'genres',
            'user.profile.city',
            'user.profile.instruments',
            'user.profile.genres',
        ])->findOrFail($id);

        $instrumentIds = $this->announcement->instruments->pluck('id');
        $genreIds = $this->announcement->genres->pluck('id');

        $this->related = Announcement::with(['city', 'instruments', 'genres'])
            ->active()
            ->where('id', '!=', $this->announcement->id)
            ->where(function ($q) use ($instrumentIds, $genreIds) {
                $q->whereHas('instruments', fn ($q2) => $q2->whereIn('id', $instrumentIds))
                    ->orWhereHas('genres', fn ($q2) => $q2->whereIn('id', $genreIds));
            })
            ->limit(3)
            ->get();
    }

    #[On('echo:profile.{announcement.user.profile.id},.contact-preference.updated')]
    public function refreshContactState(): void
    {
        $this->announcement->user->unsetRelation('profile');
    }

    public function render(): View
    {
        $description = filled($this->announcement->description)
            ? Str::limit(strip_tags($this->announcement->description), 155)
            : __('seo.descriptions.announcement', ['title' => $this->announcement->title]);

        return $this->view()
            ->title($this->announcement->title)
            ->layout('layouts.app', ['description' => $description]);
    }
};
?>

<div itemscope itemtype="https://schema.org/Event">
    <link itemprop="url" href="{{ route('announcement', ['id' => $announcement->id]) }}">

    <x-parts.announcement.hero :announcement="$announcement" />

    <div class="max-w-6xl mx-auto px-6 py-10 space-y-10">

        {{-- 2-col layout --}}
        <div class="flex flex-col lg:flex-row gap-8 items-start">

            {{-- Main content --}}
            <div class="flex-1 min-w-0 space-y-6 order-2 lg:order-1">
                <x-parts.announcement.tags :announcement="$announcement" />
                <x-parts.announcement.description :announcement="$announcement" />
            </div>

            {{-- Sidebar --}}
            <aside class="w-full lg:w-72 shrink-0 lg:sticky lg:top-24 order-1 lg:order-2" aria-label="{{ __('announcement.author_aria') }}">
                <x-parts.announcement.author-card :announcement="$announcement" :author="$announcement->user->profile" :name="$announcement->user->full_name" />
            </aside>

        </div>

        {{-- Related announcements --}}
        <x-parts.announcement.related :suggestions="$related" />

    </div>

</div>
