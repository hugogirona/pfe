@props(['media', 'musicianName', 'canEdit' => false])

@php
    /** @var \App\Models\Media $media */
    $isImage = $media->type === \App\Enums\MediaType::Image;
    $editComponent = $isImage ? 'parts.profile.add-image-form' : 'parts.profile.add-youtube-form';
    $editTitle = $isImage ? __('profile.gallery_edit_image_title') : __('profile.gallery_edit_video_title');
@endphp

<button
    type="button"
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
    class="group relative w-full aspect-4/3 rounded-xl overflow-hidden bg-pastel-taupe cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent transition-shadow duration-200 hover:shadow-lg"
    @if ($canEdit) x-bind:class="editMode ? 'ring-2 ring-accent shadow-lg' : ''" @endif
    @unless ($canEdit) aria-label="{{ $isImage ? __('profile.photo_alt', ['name' => $musicianName]) : __('profile.video_play') }}" @endunless
>
    @if ($isImage)
        <img
            src="{{ $media->display_url }}"
            alt=""
            class="absolute inset-0 w-full h-full object-cover transition-transform duration-300 motion-safe:group-hover:scale-105"
            loading="lazy"
        />
        <div class="absolute inset-0 bg-dark/0 group-hover:bg-dark/15 transition-colors duration-200" aria-hidden="true"></div>
    @else
        <div class="absolute inset-0 bg-dark" aria-hidden="true"></div>
        <img
            src="{{ $media->youtube_thumbnail_url }}"
            alt=""
            class="absolute inset-0 w-full h-full object-cover transition-transform duration-300 motion-safe:group-hover:scale-105"
            loading="lazy"
        />
        <div class="absolute inset-0 bg-dark/25 group-hover:bg-dark/40 transition-colors duration-200" aria-hidden="true"></div>
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
