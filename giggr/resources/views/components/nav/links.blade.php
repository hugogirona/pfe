<div class="hidden md:flex items-center gap-8">
    <x-nav.link href="{{ route('home') }}" :active="request()->routeIs('home')">
        Accueil
    </x-nav.link>
    <x-nav.link href="#" :active="request()->routeIs('explorer')">
        Explorer
    </x-nav.link>
    <x-nav.link href="#" :active="request()->routeIs('contact')">
        Contact
    </x-nav.link>
</div>
