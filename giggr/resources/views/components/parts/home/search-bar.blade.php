@props([
    'placeholder' => 'Recherchez un profil ou un instrument...',
    'name'        => 'q',
])

<div {{ $attributes->class(['relative flex-1']) }}>
    <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-dark/35">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0Z" />
        </svg>
    </span>
    <input
        type="search"
        name="{{ $name }}"
        placeholder="{{ $placeholder }}"
        class="w-full pl-10 pr-4 py-2.5 text-sm bg-bg border border-dark/15 rounded-[6px] text-dark placeholder:text-dark/35 focus:outline-none focus:ring-1 focus:ring-accent transition-colors duration-150"
    />
</div>
