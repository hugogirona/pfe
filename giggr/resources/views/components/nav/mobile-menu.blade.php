<div class="md:hidden"
     x-data="{ open: false }"
     x-init="$watch('open', val => {
         if (val) {
             document.body.style.overflow = 'hidden';
         } else {
             setTimeout(() => document.body.style.overflow = '', 200);
         }
     })">

    <button @click="open = !open"
            :aria-expanded="open"
            class="relative z-50 text-dark/60 hover:text-dark transition-colors duration-150 p-2 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-[6px]"
            aria-label="{{ __('nav.aria_menu') }}">
        <span class="flex flex-col justify-center gap-1.25 w-5 h-5">
            <span class="block h-0.5 w-5 bg-current rounded-full transition-all duration-300 ease-in-out origin-center"
                  :class="open ? 'rotate-45 translate-y-1.75' : ''"></span>
            <span class="block h-0.5 w-5 bg-current rounded-full transition-all duration-300 ease-in-out"
                  :class="open ? 'opacity-0 scale-x-0' : ''"></span>
            <span class="block h-0.5 w-5 bg-current rounded-full transition-all duration-300 ease-in-out origin-center"
                  :class="open ? '-rotate-45 -translate-y-1.75' : ''"></span>
        </span>
    </button>

    <nav x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="-translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="-translate-y-full"
         class="fixed inset-0 z-40 bg-bg flex flex-col items-center justify-center gap-6"
         aria-label="{{ __('nav.aria_mobile_nav') }}">

        <x-nav.mobile-link href="{{ route('home') }}">{{ __('nav.home') }}</x-nav.mobile-link>
        <x-nav.mobile-link href="{{ route('explore') }}">{{ __('nav.explore') }}</x-nav.mobile-link>
        <x-nav.mobile-link href="{{ route('contact') }}">{{ __('nav.contact') }}</x-nav.mobile-link>

        @auth
            <div class="absolute bottom-10 left-6 right-6 flex flex-col gap-3">
                <x-cta href="{{ route('profile', ['id' => auth()->user()->id]) }}" wire:navigate variant="outline" class="w-full min-h-11 text-base">
                    {{ __('nav.view_profile') }}
                </x-cta>
                <form method="POST" action="/logout">
                    @csrf
                    <x-cta type="submit" variant="dark" class="w-full min-h-11 text-base">
                        {{ __('nav.sign_out') }}
                    </x-cta>
                </form>
            </div>
        @endauth

        @guest
            <div class="absolute bottom-10 left-6 right-6 flex flex-col gap-3">
                <x-cta href="{{ route('register') }}" wire:navigate variant="dark" class="w-full min-h-11 text-base">{{ __('nav.sign_up') }}</x-cta>
                <x-cta href="{{ route('login') }}" wire:navigate variant="outline" class="w-full min-h-11 text-base">{{ __('nav.sign_in') }}</x-cta>
            </div>
        @endguest

    </nav>
</div>
