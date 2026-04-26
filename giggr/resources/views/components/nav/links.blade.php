<div class="hidden md:flex items-center gap-8">
    <x-nav.link href="{{ $localeRoute('home') }}" :active="request()->routeIs('home', 'en.home')">
        {{ __('nav.home') }}
    </x-nav.link>
    <x-nav.link href="#" :active="request()->routeIs('explorer')">
        {{ __('nav.explore') }}
    </x-nav.link>
    <x-nav.link href="#" :active="request()->routeIs('contact')">
        {{ __('nav.contact') }}
    </x-nav.link>
</div>
