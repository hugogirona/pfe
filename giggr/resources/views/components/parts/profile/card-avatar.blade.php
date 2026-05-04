@props(['profile', 'isOwner' => false])

@php
    $name = $profile->user->full_name;
    $circle = 'w-28 h-28 rounded-full overflow-hidden bg-pastel-blue ring-4 ring-bg shadow-md';
@endphp

<div class="flex flex-col items-center pt-8 pb-4 px-6">
    <div class="relative">

        @if ($isOwner)
            <button
                type="button"
                @click="$wire.dispatch('open-modal', {
                    component: 'parts.profile.avatar-form',
                    title: '{{ __('profile.avatar_upload_title') }}',
                    model_id: '{{ $profile->id }}'
                })"
                class="group relative {{ $circle }} cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-2"
                aria-label="{{ __('profile.avatar_edit') }}"
            >
                <x-parts.profile.card-avatar-inner :profile="$profile" :name="$name"/>

                <div
                    class="absolute inset-0 bg-dark/40 opacity-0 group-hover:opacity-100 motion-safe:transition-opacity motion-safe:duration-150 flex items-center justify-center"
                    aria-hidden="true">
                    <x-icon name="pencil-square" class="w-6 h-6 text-bg"/>
                </div>
            </button>
        @else
            <div class="{{ $circle }}">
                <x-parts.profile.card-avatar-inner :profile="$profile" :name="$name"/>
            </div>
        @endif

        <span class="absolute bottom-1 right-1 w-4 h-4 rounded-full bg-accent ring-2 ring-bg" aria-hidden="true"></span>
    </div>
</div>
