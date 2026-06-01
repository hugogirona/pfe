@props(['media', 'musicianName', 'canEdit' => false])

@php
    /** @var \App\Models\Media $media */
    $isImage = $media->type === \App\Enums\MediaType::Image;
    $isProcessing = $media->isProcessing();
    $editComponent = $isImage ? 'parts.profile.add-image-form' : 'parts.profile.add-youtube-form';
    $editTitle = $isImage ? __('profile.gallery_edit_image_title') : __('profile.gallery_edit_video_title');
@endphp

<button
    type="button"
    @if ($isProcessing) disabled aria-busy="true" @endif
    @click="
        @if ($canEdit)
            if (editMode) {
                $dispatch('open-modal', {
                    component: '{{ $editComponent }}',
                    title: @js($editTitle),
                    media_id: '{{ $media->id }}',
                });
                return;
            }
        @endif
        $dispatch('open-media-lightbox', { mediaId: {{ $media->id }} });
    "
    @if ($canEdit) x-bind:aria-label="editMode
    ? @js(__('profile.gallery_edit_item'))
    : @js($isImage ? __('profile.photo_alt', ['name' => $musicianName]) : __('profile.video_play'))"
    @endif
    class="group relative w-full aspect-4/3 rounded-xl overflow-hidden bg-pastel-taupe focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent transition-shadow duration-200 {{ $isProcessing ? 'cursor-wait' : 'cursor-pointer hover:shadow-lg' }}"
    @if ($canEdit) x-bind:class="editMode ? 'ring-2 ring-accent shadow-lg' : ''" @endif
    @unless ($canEdit) aria-label="{{ $isImage ? __('profile.photo_alt', ['name' => $musicianName]) : __('profile.video_play') }}" @endunless
>
    @if ($isProcessing)
        <span class="absolute inset-0 flex items-center justify-center bg-dark/5 animate-pulse" aria-hidden="true">
            <svg class="w-8 h-8 text-dark/30 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
        </span>
        <span class="sr-only">{{ __('profile.media_processing') }}</span>
    @elseif ($isImage)
        <img
            src="{{ $media->display_url }}"
            alt=""
            class="absolute inset-0 w-full h-full object-cover transition-transform duration-300 motion-safe:group-hover:scale-105"
            loading="lazy"
        />
        <span class="absolute inset-0 bg-dark/0 group-hover:bg-dark/15 transition-colors duration-200" aria-hidden="true"></span>
    @else
        <span class="absolute inset-0 bg-dark" aria-hidden="true"></span>
        <img
            src="{{ $media->youtube_thumbnail_url }}"
            alt=""
            class="absolute inset-0 w-full h-full object-cover transition-transform duration-300 motion-safe:group-hover:scale-105"
            loading="lazy"
        />
        <span class="absolute inset-0 bg-dark/25 group-hover:bg-dark/40 transition-colors duration-200" aria-hidden="true"></span>
        <span class="absolute inset-0 flex items-center justify-center" aria-hidden="true">
            <span class="w-14 h-14 rounded-full bg-bg/90 backdrop-blur-sm flex items-center justify-center shadow-lg group-hover:bg-bg group-hover:scale-110 transition-all duration-200 motion-safe:transition-transform">
                <x-icon name="play" class="w-5 h-5 text-dark ml-0.5"/>
            </span>
        </span>
    @endif

    @if ($canEdit)
        <span
            x-show="editMode"
            x-cloak
            x-transition.opacity
            class="absolute top-2 right-2 w-9 h-9 flex items-center justify-center rounded-full bg-accent text-bg shadow-md"
            aria-hidden="true"
        >
            <x-icon name="pencil-square" class="w-4 h-4"/>
        </span>
    @endif
</button>
