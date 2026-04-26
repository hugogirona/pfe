@props([
    'placeholder' => null,
    'name'        => 'q',
])

<div {{ $attributes->class(['relative flex-1']) }}>
    <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2">
        <x-icon name="search" class="w-4 h-4 text-dark/35" />
    </span>
    <input
        type="search"
        name="{{ $name }}"
        placeholder="{{ $placeholder ?? __('home.search_placeholder') }}"
        class="w-full h-full pl-10 pr-4 py-2.5 text-base bg-bg border border-dark/15 rounded-[6px] text-dark placeholder:text-dark/35 focus:outline-none focus:ring-1 focus:ring-accent transition-colors duration-150"
    />
</div>
