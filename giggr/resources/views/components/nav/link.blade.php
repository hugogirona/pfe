@props(['href' => '#', 'exact' => true])

<a
    href="{{ $href }}"
    wire:navigate.hover
    @if ($exact)
        wire:current.exact="!text-accent"
    @else
        wire:current="!text-accent"
    @endif
    class="text-base font-medium text-dark/45 hover:text-dark transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-sm"
>
    {{ $slot }}
</a>
