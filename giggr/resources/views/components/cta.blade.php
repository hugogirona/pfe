@props([
    'variant' => 'dark',
    'size'    => 'base',
    'href'    => null,
    'type'    => 'button',
])

@php
$sizes = [
    'icon-sm'    => 'w-6 h-6 p-0 rounded-[6px]',
    'icon'       => 'w-7 h-7 p-0 rounded-[6px]',
    'icon-round' => 'w-9 h-9 p-0 rounded-full',
    'sm'         => 'px-3 py-1.5 text-xs font-medium rounded-md',
    'xs'         => 'px-3.5 py-1.5 text-xs font-semibold rounded-[6px]',
    'base'       => 'px-5 py-2 text-base font-medium rounded-[6px]',
    'form'       => 'h-11 text-sm font-medium rounded-md',
    'lg'         => 'px-8 py-4 text-base font-medium rounded-[6px]',
];

$variants = [
    'simple'       => 'border-transparent text-caption hover:text-body hover:bg-dark/5 transition-colors duration-150',
    'outline'      => 'border-dark/30 text-body hover:border-dark hover:bg-dark/5 transition-colors duration-150',
    'dark'         => 'border-transparent bg-dark text-on-dark hover:opacity-80 transition-opacity duration-150',
    'accent'       => 'border-transparent bg-accent text-on-dark hover:opacity-90 transition-opacity duration-150',
    'danger'       => 'border-transparent text-danger hover:bg-danger/10 transition-colors duration-150',
    'danger-solid' => 'border-transparent bg-danger text-on-dark hover:opacity-90 transition-opacity duration-150',
];

$base = [
    'inline-flex items-center justify-center border cursor-pointer',
    'focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent',
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
