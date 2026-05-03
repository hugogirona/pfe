@props(['profile', 'isOwner' => false])

<section aria-labelledby="gallery-heading" class="bg-white rounded-2xl border border-dark/10 shadow-sm p-6 md:p-8">

    <div class="flex items-center justify-between mb-6">
        <h2 id="gallery-heading" class="font-heading text-2xl text-dark">
            {{ __('profile.gallery_title') }}
        </h2>
        @if ($isOwner)
            <div x-data="{ tip: false }" class="relative">
                <x-cta
                    variant="simple"
                    size="icon"
                    @click="tip = !tip" @focus="tip = true" @blur="tip = false"
                    aria-label="{{ __('profile.add_media') }}"
                >
                    <x-icon name="plus" class="w-4 h-4" />
                </x-cta>
                <div
                    x-show="tip"
                    x-cloak
                    @click.outside="tip = false"
                    class="absolute right-0 top-9 z-10 bg-dark text-bg text-xs px-3 py-2 rounded-lg whitespace-nowrap shadow-lg"
                >
                    {{ __('profile.media_coming_soon') }}
                </div>
            </div>
        @endif
    </div>

    @if ($isOwner)
        <p class="text-sm text-dark/40 italic">{{ __('profile.gallery_empty_owner') }}</p>
    @else
        <p class="text-sm text-dark/40 italic">{{ __('profile.gallery_empty') }}</p>
    @endif

</section>
