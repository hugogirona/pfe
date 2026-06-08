<div class="max-w-6xl mx-auto px-6 pt-6">
    <a
        wire:navigate
        href="{{ route('explore', ['tab' => __('explore.tab_profiles_slug')]) }}"
        class="inline-flex items-center gap-1.5 text-sm text-subtle hover:text-body transition-colors duration-150 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded"
    >
        <x-icon name="arrow-right" class="w-3.5 h-3.5 rotate-180"/>
        {{ __('profile.back_to_explore') }}
    </a>
</div>
