@props(['href' => '#'])

<a
    href="{{ $href }}"
    wire:navigate
    wire:current.exact="!text-dark"
    @click="open = false"
    class="text-3xl font-bold text-dark/30 hover:text-dark/60 transition-colors duration-150 cursor-pointer"
>{{ $slot }}</a>
