@props(['profile', 'isOwner' => false])

<section
    aria-labelledby="gallery-heading"
    class="bg-white rounded-2xl border border-dark/10 shadow-sm p-6 md:p-8"
    x-data="{ editMode: false }"
>

    <div class="flex items-center justify-between mb-6">
        <h2 id="gallery-heading" class="font-heading text-2xl text-dark">
            {{ __('profile.gallery_title') }}
        </h2>
        @if ($isOwner && $profile->media->isNotEmpty())
            <div class="flex items-center gap-2">

                <x-cta
                    variant="simple"
                    size="icon"
                    @click="editMode = !editMode"
                    x-bind:class="editMode ? 'bg-accent/15 ring-1 ring-accent text-accent' : ''"
                    x-bind:aria-pressed="editMode"
                    aria-label="{{ __('profile.gallery_edit_mode') }}"
                >
                    <x-icon name="pencil-square" class="w-4 h-4"/>
                </x-cta>

                <div class="relative" x-data="{ open: false }" x-show="!editMode">
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
                        <li>
                            <button
                                type="button"
                                @click="
                                    open = false;
                                    $dispatch('open-modal', {
                                        component: 'parts.profile.add-image-form',
                                        title: '{{ __('profile.gallery_add_image_title') }}',
                                        model_id: '{{ $profile->id }}',
                                    });
                                "
                                class="w-full flex items-center gap-2.5 px-4 py-3 text-sm text-dark hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:bg-dark/5"
                            >
                                <x-icon name="photo" class="w-4 h-4 text-dark/40"/>
                                <span>{{ __('profile.gallery_add_image') }}</span>
                            </button>
                        </li>

                        <li>
                            <button
                                type="button"
                                @click="
                                    open = false;
                                    $dispatch('open-modal', {
                                        component: 'parts.profile.add-youtube-form',
                                        title: '{{ __('profile.gallery_add_video_title') }}',
                                        model_id: '{{ $profile->id }}',
                                    });
                                "
                                class="w-full flex items-center gap-2.5 px-4 py-3 text-sm text-dark hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:bg-dark/5"
                            >
                                <x-icon name="play" class="w-4 h-4 text-dark/40"/>
                                <span>{{ __('profile.gallery_add_video') }}</span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        @elseif ($isOwner)
            <div class="relative" x-data="{ open: false }">
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
                    <li>
                        <button
                            type="button"
                            @click="
                                open = false;
                                $dispatch('open-modal', {
                                    component: 'parts.profile.add-image-form',
                                    title: '{{ __('profile.gallery_add_image_title') }}',
                                    model_id: '{{ $profile->id }}',
                                });
                            "
                            class="w-full flex items-center gap-2.5 px-4 py-3 text-sm text-dark hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:bg-dark/5"
                        >
                            <x-icon name="photo" class="w-4 h-4 text-dark/40"/>
                            <span>{{ __('profile.gallery_add_image') }}</span>
                        </button>
                    </li>
                    <li>
                        <button
                            type="button"
                            @click="
                                open = false;
                                $dispatch('open-modal', {
                                    component: 'parts.profile.add-youtube-form',
                                    title: '{{ __('profile.gallery_add_video_title') }}',
                                    model_id: '{{ $profile->id }}',
                                });
                            "
                            class="w-full flex items-center gap-2.5 px-4 py-3 text-sm text-dark hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:bg-dark/5"
                        >
                            <x-icon name="play" class="w-4 h-4 text-dark/40"/>
                            <span>{{ __('profile.gallery_add_video') }}</span>
                        </button>
                    </li>
                </ul>
            </div>
        @endif
    </div>

    @if ($isOwner && $profile->media->isNotEmpty())
        <p x-show="editMode" x-cloak class="text-xs text-accent mb-4 italic">
            {{ __('profile.gallery_edit_hint_owner') }}
        </p>
    @endif

    @if ($profile->media->isEmpty())
        <p class="text-sm text-dark/40 italic">
            {{ $isOwner ? __('profile.gallery_empty_owner') : __('profile.gallery_empty') }}
        </p>
    @else
        @php $musicianName = $profile->user->full_name; @endphp
        <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($profile->media as $media)
                <li>
                    <x-parts.profile.media-item
                        :media="$media"
                        :musician-name="$musicianName"
                        :can-edit="$isOwner"
                    />
                </li>
            @endforeach
        </ul>
    @endif

</section>
