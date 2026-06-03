@props(['href' => '#', 'exact' => true])

<a
    href="{{ $href }}"
    wire:navigate
    @if ($exact)
        wire:current.exact="!text-body"
    @else
        wire:current="!text-body"
    @endif
    @click="open = false"
    class="text-3xl font-bold text-caption hover:text-subtle transition-colors duration-150 cursor-pointer"
>{{ $slot }}</a>
