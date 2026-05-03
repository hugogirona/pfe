@props([
    'variant' => 'dark',
    'size'    => 'base',
    'href'    => null,
    'type'    => 'button',
])

@php
$sizes = [
    'icon-sm' => 'w-6 h-6 p-0',
    'icon'    => 'w-7 h-7 p-0',
    'xs'      => 'px-3.5 py-1.5 text-xs font-semibold',
    'base'    => 'px-5 py-2 text-base font-medium',
    'lg'      => 'px-8 py-4 text-base font-medium',
];

$variants = [
    'simple'  => 'border-transparent text-dark/40 hover:text-dark hover:bg-dark/5 transition-colors duration-150',
    'outline' => 'border-dark/30 text-dark hover:border-dark hover:bg-dark/5 transition-colors duration-150',
    'dark'    => 'border-transparent bg-dark text-bg hover:opacity-80 transition-opacity duration-150',
    'accent'  => 'border-transparent bg-accent text-bg hover:opacity-90 transition-opacity duration-150',
    'danger'  => 'border-transparent text-danger hover:bg-danger/10 transition-colors duration-150',
];

$base = [
    'inline-flex items-center justify-center rounded-[6px] border cursor-pointer',
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
