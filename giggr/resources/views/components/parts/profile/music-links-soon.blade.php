<section {{ $attributes->class('px-6 py-5') }}>
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-[0.6875rem] font-semibold uppercase tracking-widest text-caption">
            {{ __('profile.music_links_label') }}
        </h3>
        <span class="inline-flex items-center rounded-full bg-dark/5 px-2 py-0.5 text-[0.625rem] font-semibold uppercase tracking-wide text-caption">
            {{ __('profile.music_links_soon') }}
        </span>
    </div>

    <button
        type="button"
        disabled
        aria-disabled="true"
        title="{{ __('profile.music_links_soon') }}"
        class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg border border-dashed border-dark/15 text-sm font-medium text-subtle opacity-60 cursor-not-allowed"
    >
        <x-icon name="music-note" class="w-4 h-4" />
        {{ __('profile.music_links_add') }}
    </button>
</section>
