@props(['profile', 'isOwner' => false])

<section aria-labelledby="gallery-heading" class="bg-white rounded-2xl border border-dark/10 shadow-sm p-6 md:p-8">

    <div class="flex items-center justify-between mb-6">
        <h2 id="gallery-heading" class="font-heading text-2xl text-dark">
            {{ __('profile.gallery_title') }}
        </h2>
        @if ($isOwner)
            <div class="relative" x-data="{ open: false }">
                <x-cta
                    variant="simple"
                    size="icon"
                    @click="open = !open"
                    @keydown.escape.window="open = false"
                    aria-expanded="false"
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
                    {{-- Add image (disabled until #3 ships) --}}
                    <li>
                        <button
                            type="button"
                            disabled
                            class="w-full flex items-center gap-2.5 px-4 py-3 text-sm text-dark/30 cursor-not-allowed"
                        >
                            <x-icon name="photo" class="w-4 h-4 text-dark/20"/>
                            <span class="flex-1 text-left">{{ __('profile.gallery_add_image') }}</span>
                            <span class="text-[10px] font-medium uppercase tracking-wide bg-pastel-taupe text-dark/55 rounded-full px-2 py-0.5">
                                {{ __('profile.gallery_soon') }}
                            </span>
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

    @if ($isOwner)
        <p class="text-sm text-dark/40 italic">{{ __('profile.gallery_empty_owner') }}</p>
    @else
        <p class="text-sm text-dark/40 italic">{{ __('profile.gallery_empty') }}</p>
    @endif

</section>
