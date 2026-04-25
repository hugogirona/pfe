@props(['href' => '#', 'active' => false])

<a href="{{ $href }}"
   @class([
       'nav-link text-m font-medium text-dark',
       'nav-link--active' => $active,
   ])>
    {{ $slot }}
</a>
