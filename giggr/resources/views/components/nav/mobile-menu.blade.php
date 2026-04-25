<div class="md:hidden" x-data="{ open: false }">

    <button @click="open = !open" class="text-dark p-1" aria-label="Menu">
        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
        <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>
    </button>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="absolute top-16 left-0 right-0 bg-bg border-b border-dark/10 px-6 py-4 flex flex-col gap-4">

        <x-nav.link href="{{ route('home') }}" :active="request()->routeIs('home')">
            Accueil
        </x-nav.link>
        <x-nav.link href="#" :active="request()->routeIs('explorer')">
            Explorer
        </x-nav.link>
        <x-nav.link href="#" :active="request()->routeIs('contact')">
            Contact
        </x-nav.link>

        <hr class="border-dark/10">

        @auth
            <a href="#" class="text-sm font-medium text-dark hover:text-accent transition-colors duration-150">
                Mon profil
            </a>
        @else
            <a href="#" class="text-sm font-medium text-dark hover:text-accent transition-colors duration-150">
                Connexion
            </a>
        @endauth

    </div>
</div>
