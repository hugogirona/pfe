@props(['profile', 'isOwner' => false])

@php
    $name = $profile->user->full_name;
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
                class="group relative w-28 h-28 rounded-full overflow-hidden bg-pastel-blue ring-4 ring-bg shadow-md cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-2"
                aria-label="{{ __('profile.avatar_edit') }}"
            >
                @if ($profile->thumbnail)
                    <img
                        src="{{ $profile->thumbnail }}"
                        alt="{{ __('profile.avatar_alt', ['name' => $name]) }}"
                        class="w-full h-full object-cover object-center"
                    />
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <span class="font-heading text-4xl text-dark/30 select-none">
                            {{ mb_substr($name, 0, 1) }}
                        </span>
                    </div>
                @endif

                <div class="absolute inset-0 bg-dark/40 opacity-0 group-hover:opacity-100 motion-safe:transition-opacity motion-safe:duration-150 flex items-center justify-center" aria-hidden="true">
                    <x-icon name="pencil-square" class="w-6 h-6 text-bg" />
                </div>
            </button>
        @else
            <div class="w-28 h-28 rounded-full overflow-hidden bg-pastel-blue ring-4 ring-bg shadow-md">
                @if ($profile->medium)
                    <img
                        src="{{ $profile->medium }}"
                        alt="{{ __('profile.avatar_alt', ['name' => $name]) }}"
                        class="w-full h-full object-cover object-center"
                    />
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <span class="font-heading text-4xl text-dark/30 select-none">
                            {{ mb_substr($name, 0, 1) }}
                        </span>
                    </div>
                @endif
            </div>
        @endif

        <span class="absolute bottom-1 right-1 w-4 h-4 rounded-full bg-accent ring-2 ring-bg" aria-hidden="true"></span>
    </div>
</div>
