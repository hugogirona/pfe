@props(['user', 'name'])

<header class="flex items-center gap-3 px-5 py-3 border-b border-dark/10 shrink-0 bg-bg">
    <button
        type="button"
        wire:click="backToList"
        class="w-9 h-9 flex items-center justify-center rounded-full text-dark/60 hover:text-dark hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
        aria-label="{{ __('messaging.back') }}"
    >
        <x-icon name="arrow-right" class="w-5 h-5 rotate-180"/>
    </button>
    <x-parts.messaging.avatar :user="$user" class="w-9 h-9 text-sm"/>
    <h3 id="messaging-thread-heading" class="text-sm font-medium text-dark truncate">{{ $name }}</h3>
</header>
