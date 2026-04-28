@props(['href' => '#', 'active' => false])

<a href="{{ $href }}"
   @click="open = false"
   @class([
       'text-3xl font-bold transition-colors duration-150 cursor-pointer',
       'text-dark'                       => $active,
       'text-dark/30 hover:text-dark/60' => !$active,
   ])>{{ $slot }}</a>
