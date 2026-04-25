@props([
    'variant' => 'dark',
    'size'    => 'base',
    'href'    => '#',
])

@php
$sizes = [
    'base' => 'px-6 py-2 text-m',
    'lg'   => 'px-7 py-3 text-m',
];

$variants = [
    'outline' => 'text-dark border border-dark hover:bg-dark/5 transition-colors duration-150',
    'dark'    => 'text-bg bg-dark hover:opacity-80 transition-opacity duration-150',
];
@endphp

<a href="{{ $href }}"
   {{ $attributes->class([
       'inline-flex items-center justify-center font-medium rounded-[6px]',
       $sizes[$size],
       $variants[$variant],
   ]) }}>
    {{ $slot }}
</a>
