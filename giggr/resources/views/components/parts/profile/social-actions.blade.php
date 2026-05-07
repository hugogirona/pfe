@props(['profile'])

<div class="space-y-2.5">
    <x-cta variant="accent" class="w-full gap-2 py-2.5" aria-label="{{ __('profile.contact_name', ['name' => $profile->user->full_name]) }}">
        <x-icon name="chat-bubble" class="w-4 h-4" />
        {{ __('profile.contact') }}
    </x-cta>

    <livewire:parts.social.follow-button
        :profile-id="$profile->id"
        variant="button"
        :wire:key="'follow-profile-button-'.$profile->id"
    />
</div>
