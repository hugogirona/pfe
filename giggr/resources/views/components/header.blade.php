<header class="sticky top-0 z-50 bg-bg border-b border-dark/10">
    <h1 class="sr-only">{{ __('nav.site_title') }}</h1>
    <div class="border-b border-dark/10">
        <div class="max-w-6xl mx-auto px-6 py-1.5 flex items-center justify-end">
            <x-footer.lang-switcher variant="light" />
        </div>
    </div>
    <nav class="max-w-6xl mx-auto px-6 py-4 flex items-center gap-10 relative"
         aria-label="{{ __('nav.aria_main_nav') }}">
        <h2 class="sr-only">{{ __('nav.aria_main_nav') }}</h2>
        <a wire:navigate.hover href="{{route('home')}}" aria-label="{{ __('nav.aria_logo_home') }}" class="focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-sm">
            <x-logo />
        </a>
        <x-nav.links />

        <div class="ml-auto flex items-center gap-2 md:gap-3">
            @auth
                <livewire:parts.layout.messaging-badge />
            @endauth
            <x-nav.actions />
            <x-nav.mobile-menu />
        </div>
    </nav>

    <noscript>
        <nav class="border-t border-dark/10 bg-bg" aria-label="{{ __('nav.aria_fallback_nav') }}">
            <ul class="max-w-6xl mx-auto px-6 py-3 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-subtle">
                <li class="md:hidden"><a href="{{ route('home') }}" class="hover:text-body hover:underline underline-offset-4">{{ __('nav.home') }}</a></li>
                <li class="md:hidden"><a href="{{ route('explore') }}" class="hover:text-body hover:underline underline-offset-4">{{ __('nav.explore') }}</a></li>
                <li class="md:hidden"><a href="{{ route('contact') }}" class="hover:text-body hover:underline underline-offset-4">{{ __('nav.contact') }}</a></li>
                @auth
                    <li><a href="{{ route('profile', ['id' => auth()->id()]) }}" class="hover:text-body hover:underline underline-offset-4">{{ __('nav.profile') }}</a></li>
                    <li><a href="{{ route('settings.account') }}" class="hover:text-body hover:underline underline-offset-4">{{ __('nav.settings') }}</a></li>
                    <li>
                        <form method="POST" action="/logout">
                            @csrf
                            <button type="submit" class="text-danger/70 hover:text-danger hover:underline underline-offset-4 cursor-pointer">{{ __('nav.sign_out') }}</button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}" class="hover:text-body hover:underline underline-offset-4">{{ __('nav.sign_in') }}</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-body hover:underline underline-offset-4">{{ __('nav.sign_up') }}</a></li>
                @endauth
            </ul>
        </nav>
    </noscript>
</header>
