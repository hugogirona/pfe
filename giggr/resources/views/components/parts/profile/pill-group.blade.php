@props([
    'items'    => [],
    'selected' => [],
    'method',
    'variant'  => 'dark',
])

@php
    $activeClass = $variant === 'accent'
        ? 'bg-accent text-bg border-accent'
        : 'bg-dark text-bg border-dark';
@endphp

<div class="flex flex-wrap gap-2" role="group">
    @foreach ($items as $item)
        <button
            type="button"
            wire:click="{{ $method }}('{{ $item }}')"
            @class([
                'h-9 px-4 rounded-full border text-sm font-medium transition-all duration-150 cursor-pointer',
                'focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent',
                $activeClass                                                                    => in_array($item, $selected),
                'bg-white text-dark/60 border-dark/15 hover:border-dark/40 hover:text-dark'    => !in_array($item, $selected),
            ])
            aria-pressed="{{ in_array($item, $selected) ? 'true' : 'false' }}"
        >{{ $item }}</button>
    @endforeach
</div>
