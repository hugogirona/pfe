@props([
    'profile',
    'followersCount',
    'followedCount',
])

<div class="flex items-center justify-center gap-1 py-3 border-b border-dark/[0.07] text-sm">
    <button
        type="button"
        x-data
        @click.stop="$dispatch('open-relations-modal', { profileId: {{ $profile->id }}, tab: 'followers' })"
        class="flex items-baseline gap-1.5 px-3 py-1.5 rounded-md text-body hover:text-accent transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
        aria-label="{{ trans_choice('social.count_followers', $followersCount, ['n' => $followersCount]) }}"
    >
        <span class="font-heading text-base">{{ $followersCount }}</span>
        <span class="text-subtle">{{ __('social.tab_followers') }}</span>
    </button>

    <span class="text-subtle select-none" aria-hidden="true">·</span>

    <button
        type="button"
        x-data
        @click.stop="$dispatch('open-relations-modal', { profileId: {{ $profile->id }}, tab: 'followed' })"
        class="flex items-baseline gap-1.5 px-3 py-1.5 rounded-md text-body hover:text-accent transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
        aria-label="{{ trans_choice('social.count_followed', $followedCount, ['n' => $followedCount]) }}"
    >
        <span class="font-heading text-base">{{ $followedCount }}</span>
        <span class="text-subtle">{{ __('social.tab_followed') }}</span>
    </button>
</div>
