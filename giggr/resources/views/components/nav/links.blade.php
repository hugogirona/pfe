<div class="hidden md:flex items-center gap-8">
    <x-nav.link href="{{ route('home') }}" :active="request()->routeIs('home')">
        {{ __('nav.home') }}
    </x-nav.link>
    <x-nav.link href="{{ route('explore') }}" :active="request()->routeIs('explore')">
        {{ __('nav.explore') }}
    </x-nav.link>
    <x-nav.link href="#" :active="request()->routeIs('contact')">
        {{ __('nav.contact') }}
    </x-nav.link>
</div>
