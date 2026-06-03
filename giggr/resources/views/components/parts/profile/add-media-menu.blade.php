@props(['profile'])

<div {{ $attributes->merge(['class' => 'relative']) }} x-data="{ open: false }">
    <x-cta
        variant="simple"
        size="icon"
        @click="open = !open"
        @keydown.escape.window="open = false"
        x-bind:aria-expanded="open"
        aria-haspopup="true"
        aria-label="{{ __('profile.add_media') }}"
    >
        <x-icon name="plus" class="w-4 h-4"/>
    </x-cta>

    <ul
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
        @click.outside="open = false"
        class="absolute right-0 mt-2 w-52 bg-white rounded-xl border border-dark/10 shadow-lg overflow-hidden z-10 divide-y divide-dark/8"
        style="display: none"
    >
        {{-- Add image --}}
        <li>
            <button
                type="button"
                @click="
                    open = false;
                    $dispatch('open-modal', {
                        component: 'parts.profile.add-image-form',
                        title: @js(__('profile.gallery_add_image_title')),
                        model_id: '{{ $profile->id }}',
                    });
                "
                class="w-full flex items-center gap-2.5 px-4 py-3 text-sm text-body hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:bg-dark/5"
            >
                <x-icon name="photo" class="w-4 h-4 text-caption"/>
                <span>{{ __('profile.gallery_add_image') }}</span>
            </button>
        </li>

        {{-- Add YouTube video --}}
        <li>
            <button
                type="button"
                @click="
                    open = false;
                    $dispatch('open-modal', {
                        component: 'parts.profile.add-youtube-form',
                        title: @js(__('profile.gallery_add_video_title')),
                        model_id: '{{ $profile->id }}',
                    });
                "
                class="w-full flex items-center gap-2.5 px-4 py-3 text-sm text-body hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:bg-dark/5"
            >
                <x-icon name="play" class="w-4 h-4 text-caption"/>
                <span>{{ __('profile.gallery_add_video') }}</span>
            </button>
        </li>
    </ul>
</div>
