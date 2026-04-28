@props([
    'variant' => 'dark',
    'size'    => 'base',
    'href'    => '#',
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
@endphp

<a href="{{ $href }}"
   {{ $attributes->class([
       'inline-flex items-center justify-center font-medium rounded-[6px] cursor-pointer',
       $sizes[(string) $size],
       $variants[(string) $variant],
   ]) }}>
    {{ $slot }}
</a>
