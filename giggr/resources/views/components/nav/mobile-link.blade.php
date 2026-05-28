@props(['href' => '#', 'exact' => true])

<a
    href="{{ $href }}"
    wire:navigate
    @if ($exact)
        wire:current.exact="!text-dark"
    @else
        wire:current="!text-dark"
    @endif
    @click="open = false"
    class="text-3xl font-bold text-dark/30 hover:text-dark/60 transition-colors duration-150 cursor-pointer"
>{{ $slot }}</a>
