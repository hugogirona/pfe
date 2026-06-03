@props([
    'profile',
    'showFollowButton' => false,
    'isFollowing' => false,
    'viewerId' => null,
])

@php
    $name = $profile->user->full_name;
    $url = route('profile', ['id' => $profile->id]);
    $isSelf = $viewerId !== null && (int) $viewerId === (int) $profile->user_id;
@endphp

<div class="group flex items-center gap-3 px-4 py-3 hover:bg-pastel-salmon/40 transition-colors duration-150">
    <a
        href="{{ $url }}"
        @auth wire:navigate @endauth
        @click="$wire.close && $wire.close()"
        class="flex items-center gap-3 flex-1 min-w-0 focus-visible:outline-none focus-visible:rounded-md"
    >
        <div class="w-10 h-10 rounded-full overflow-hidden shrink-0 bg-pastel-taupe flex items-center justify-center">
            @if ($profile->thumbnail)
                <img
                    src="{{ $profile->thumbnail }}"
                    alt="{{ __('profile.avatar_alt', ['name' => $name]) }}"
                    class="w-full h-full object-cover"
                    loading="lazy"
                />
            @else
                <span class="font-heading text-base text-subtle select-none">{{ mb_substr($name, 0, 1) }}</span>
            @endif
        </div>

        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-body truncate">{{ $name }}</p>
        </div>
    </a>

    @if ($showFollowButton && ! $isSelf)
        <button
            type="button"
            wire:click="toggleFollow({{ $profile->id }})"
            @if ($isFollowing) x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false" @endif
            @class([
                'h-8 px-5 min-w-[7.5rem] rounded-full text-xs font-medium transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent shrink-0 inline-flex items-center justify-center gap-1',
                'bg-dark/5 text-subtle hover:bg-accent/10 hover:text-accent' => $isFollowing,
                'border border-dark/20 text-body hover:border-dark/40 hover:bg-pastel-salmon/40' => ! $isFollowing,
            ])
            aria-pressed="{{ $isFollowing ? 'true' : 'false' }}"
            aria-label="{{ $isFollowing ? __('social.unfollow_aria', ['name' => $name]) : __('social.follow_aria', ['name' => $name]) }}"
        >
            @if ($isFollowing)
                <span x-show="!hover">{{ __('social.following') }}</span>
                <span x-show="hover" x-cloak class="inline-flex items-center gap-1">
                    <x-icon name="x-mark" class="w-3 h-3" aria-hidden="true" />
                    {{ __('social.unfollow') }}
                </span>
            @else
                {{ __('social.follow') }}
            @endif
        </button>
    @endif
</div>
