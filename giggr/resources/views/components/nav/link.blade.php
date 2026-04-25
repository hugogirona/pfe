@props(['href' => '#', 'active' => false])

<a href="{{ $href }}"
   @class([
       'text-sm font-medium transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-sm',
       'text-dark/45 hover:text-dark' => !$active,
       'text-accent'                  => $active,
   ])>
    {{ $slot }}
</a>
