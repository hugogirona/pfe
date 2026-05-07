<?php

use App\Models\Profile;
use Livewire\Component;

new class extends Component {
    public int $profileId;

    public string $variant = 'heart';

    public string $musicianName = '';

    public ?int $ownerId = null;

    public bool $isFollowing = false;

    public bool $isOwn = false;

    public function mount(
        int     $profileId,
        string  $variant = 'heart',
        ?string $musicianName = null,
        ?int    $ownerId = null,
        ?bool   $isFollowing = null,
    ): void
    {
        $this->profileId = $profileId;
        $this->variant = $variant;

        if ($musicianName !== null && $ownerId !== null && $isFollowing !== null) {
            $this->musicianName = $musicianName;
            $this->ownerId = $ownerId;
            $this->isOwn = auth()->id() === $ownerId;
            $this->isFollowing = !$this->isOwn && $isFollowing;

            return;
        }

        $profile = Profile::with('user')->find($profileId);
        if ($profile === null) {
            return;
        }

        $this->musicianName = $musicianName ?? $profile->user->full_name;
        $this->ownerId = $ownerId ?? $profile->user_id;

        $viewer = auth()->user();
        if ($viewer === null) {
            return;
        }

        $this->isOwn = $viewer->id === $this->ownerId;
        if ($this->isOwn) {
            return;
        }

        $this->isFollowing = $isFollowing ?? $viewer->isFollowing($profile);
    }

    public function toggle(): void
    {
        abort_unless(auth()->check(), 403);

        $viewer = auth()->user();
        if ($viewer->id === $this->ownerId) {
            return;
        }

        $profile = Profile::find($this->profileId);
        if ($profile === null) {
            return;
        }

        if ($viewer->isFollowing($profile)) {
            $viewer->unfollow($profile);
            $this->isFollowing = false;
        } else {
            $viewer->follow($profile);
            $this->isFollowing = true;
        }
    }
};
?>

<div>
    @if ($isOwn)
    @elseif ($variant === 'button')
        @if (auth()->check())
            <button
                type="button"
                wire:click.stop="toggle"
                @class([
                    'w-full flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-dark/30 focus-visible:ring-offset-1',
                    'bg-dark text-bg hover:bg-dark/80' => $isFollowing,
                    'bg-transparent text-dark border border-dark/20 hover:border-dark/40 hover:bg-dark/5' => ! $isFollowing,
                ])
                aria-pressed="{{ $isFollowing ? 'true' : 'false' }}"
                aria-label="{{ $isFollowing ? __('social.unfollow_aria', ['name' => $musicianName]) : __('social.follow_aria', ['name' => $musicianName]) }}"
            >
                <x-icon name="heart" class="w-4 h-4"/>
                <span>{{ $isFollowing ? __('social.following') : __('social.follow') }}</span>
            </button>
        @else
            <button
                type="button"
                x-data
                @click.stop.prevent="$dispatch('open-auth-modal')"
                class="w-full flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium bg-transparent text-dark border border-dark/20 hover:border-dark/40 hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-dark/30 focus-visible:ring-offset-1"
                aria-label="{{ __('social.follow_aria', ['name' => $musicianName]) }}"
            >
                <x-icon name="heart" class="w-4 h-4"/>
                <span>{{ __('social.follow') }}</span>
            </button>
        @endif
    @elseif (auth()->check())
        <button
            type="button"
            wire:click.stop="toggle"
            @class([
                'w-10 h-10 flex items-center justify-center rounded-full backdrop-blur-sm transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent',
                'bg-white/90 text-accent hover:bg-white' => $isFollowing,
                'bg-white/75 text-dark/40 hover:text-accent hover:bg-white/90' => ! $isFollowing,
            ])
            aria-pressed="{{ $isFollowing ? 'true' : 'false' }}"
            aria-label="{{ $isFollowing ? __('social.unfollow_aria', ['name' => $musicianName]) : __('social.follow_aria', ['name' => $musicianName]) }}"
        >
            <span class="sr-only">{{ $isFollowing ? __('social.following') : __('social.follow') }}</span>
            <x-icon name="heart" class="w-5 h-5"/>
        </button>
    @else
        <button
            type="button"
            x-data
            @click.stop.prevent="$dispatch('open-auth-modal')"
            class="w-10 h-10 flex items-center justify-center rounded-full bg-white/75 backdrop-blur-sm text-dark/40 hover:text-accent hover:bg-white/90 transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
            aria-label="{{ __('social.follow_aria', ['name' => $musicianName]) }}"
        >
            <span class="sr-only">{{ __('social.follow') }}</span>
            <x-icon name="heart" class="w-5 h-5"/>
        </button>
    @endif
</div>
