@props(['profile'])

<div class="space-y-2.5">
    <div class="flex flex-col md:flex-row lg:flex-col gap-2.5">
        <button
            type="button"
            aria-label="{{ __('profile.contact_name', ['name' => $profile->user->full_name]) }}"
            @click="Livewire.dispatchTo('modal', 'open-modal', { component: 'parts.messaging.inbox', title: {{ json_encode(__('messaging.title')) }}, model_id: '{{ $profile->user->id }}' })"
            class="md:flex-1 lg:flex-initial flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium bg-accent text-bg hover:bg-accent/85 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
        >
            <x-icon name="chat-bubble" class="w-4 h-4"/>
            <span>{{ __('profile.contact') }}</span>
        </button>

        <div class="md:flex-1 lg:flex-initial">
            <livewire:parts.social.follow-button
                :profile-id="$profile->id"
                variant="button"
                :wire:key="'follow-profile-button-'.$profile->id"
            />
        </div>
    </div>

    <livewire:parts.profile.block-toggle
        :target-user-id="$profile->user->id"
        :wire:key="'block-toggle-'.$profile->user->id"
    />
</div>
