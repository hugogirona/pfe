@props(['profile'])

<div
    x-data="{
        relationship: 'none',

        get isFollowing()  { return this.relationship === 'following' || this.relationship === 'friend'; },
        get isFriend()     { return this.relationship === 'friend'; },
        get isPending()    { return this.relationship === 'pending'; },

        toggleFollow() {
            this.relationship = this.isFollowing && !this.isFriend ? 'none' : 'following';
        },
        handleFriend() {
            if (this.isFriend)        { this.relationship = 'following'; return; }
            if (this.isPending)       { this.relationship = 'none'; return; }
            this.relationship = 'pending';
        }
    }"
    class="space-y-2.5"
>
    <x-cta variant="accent" class="w-full gap-2 py-2.5" aria-label="{{ __('profile.contact_name', ['name' => $profile->user->full_name]) }}">
        <x-icon name="chat-bubble" class="w-4 h-4" />
        {{ __('profile.contact') }}
    </x-cta>

    <div class="flex gap-2">
        <button
            type="button"
            @click="toggleFollow()"
            :class="isFollowing
                ? 'bg-dark text-bg hover:bg-dark/80'
                : 'bg-transparent text-dark border border-dark/20 hover:border-dark/40 hover:bg-dark/5'"
            class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-dark/30 focus-visible:ring-offset-1"
            :aria-pressed="isFollowing.toString()"
        >
            <x-icon name="heart" class="w-4 h-4" />
            <span x-text="isFollowing ? '{{ __('profile.following') }}' : '{{ __('profile.follow') }}'"></span>
        </button>

        <button
            type="button"
            @click="handleFriend()"
            :class="isFriend
                ? 'bg-pastel-blue text-dark border border-pastel-blue hover:bg-dark/10'
                : isPending
                    ? 'bg-pastel-taupe text-dark border border-pastel-taupe'
                    : 'bg-transparent text-dark border border-dark/20 hover:border-dark/40 hover:bg-dark/5'"
            class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-dark/30 focus-visible:ring-offset-1"
            :aria-pressed="isFriend.toString()"
        >
            <template x-if="isFriend">
                <x-icon name="user-minus" class="w-4 h-4" />
            </template>
            <template x-if="!isFriend">
                <x-icon name="user-plus" class="w-4 h-4" />
            </template>
            <span
                x-text="isFriend
                    ? '{{ __('profile.friend') }}'
                    : isPending
                        ? '{{ __('profile.friend_pending') }}'
                        : '{{ __('profile.add_friend') }}'"
            ></span>
        </button>

    </div>
</div>
