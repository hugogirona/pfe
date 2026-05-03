@props([
    'variant' => 'instrument',
    'size'    => 'sm',
])

@php
$variants = [
    'instrument' => 'bg-dark/12 text-dark/80',
    'genre'      => 'bg-dark/6 text-dark/60',
];

$sizes = [
    'sm' => 'px-2.5 py-1 text-xs',
    'lg' => 'px-3 py-1.5 text-sm',
];
@endphp

<span {{ $attributes->class(['rounded-full font-medium', $sizes[$size], $variants[$variant]]) }}>{{ $slot }}</span>
