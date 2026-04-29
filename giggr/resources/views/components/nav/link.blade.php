@props(['href' => '#'])

<a
    href="{{ $href }}"
    wire:navigate.hover
    wire:current.exact="!text-accent"
    class="text-base font-medium text-dark/45 hover:text-dark transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-sm"
>
    {{ $slot }}
</a>
