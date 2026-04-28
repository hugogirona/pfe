@props([
    'variant' => 'dark',
    'size'    => 'base',
    'href'    => null,
    'type'    => 'button',
])

@php
$sizes = [
    'base' => 'px-5 py-2 text-base',
    'lg'   => 'px-8 py-4 text-base',
];

$variants = [
    'outline' => 'text-dark border border-dark/30 hover:border-dark hover:bg-dark/5 transition-colors duration-150',
    'dark'    => 'text-bg bg-dark hover:opacity-80 transition-opacity duration-150',
    'accent'  => 'text-bg bg-accent hover:opacity-90 transition-opacity duration-150',
];

$base = [
    'inline-flex items-center justify-center font-medium rounded-[6px] cursor-pointer',
    'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-dark/40 focus-visible:ring-offset-2',
    $sizes[(string) $size],
    $variants[(string) $variant],
];
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->class($base) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($base) }}>
        {{ $slot }}
    </button>
@endif
