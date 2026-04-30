<?php

use App\Models\Profile;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Profil — Giggr.')] class extends Component
{
    public Profile $profile;

    public function mount(int $id): void
    {
        $this->profile = Profile::with([
            'user',
            'city',
            'instruments',
            'genres',
            'user.announcements' => fn ($q) => $q->active()->with(['city', 'instruments', 'genres']),
        ])->findOrFail($id);
    }
};
?>

<div>

    <x-parts.profile.hero :profile="$profile" />

    <div class="max-w-6xl mx-auto px-6 py-10">
        <div class="flex flex-col lg:flex-row gap-8 items-start">

            {{-- Sidebar --}}
            <aside class="w-full lg:w-80 shrink-0 lg:sticky lg:top-24" aria-label="{{ $profile->user->full_name }}">
                <x-parts.profile.identity-card :profile="$profile" />
            </aside>

            {{-- Main content --}}
            <div class="flex-1 min-w-0 space-y-6">
                <x-parts.profile.about :profile="$profile" />
                <x-parts.profile.media-gallery :profile="$profile" />
                <x-parts.profile.announcements :profile="$profile" />
            </div>

        </div>
    </div>

</div>
