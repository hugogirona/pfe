<div class="hidden md:flex items-center gap-3">
    @auth
        <button class="text-dark/50 hover:text-accent transition-colors duration-150 p-2 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-[6px]"
                aria-label="{{ __('nav.aria_messaging') }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
            </svg>
        </button>
        <a href="#"
           class="w-8 h-8 rounded-full bg-accent text-bg flex items-center justify-center text-sm font-semibold uppercase cursor-pointer hover:opacity-90 transition-opacity duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-2">
            {{ substr(auth()->user()->name, 0, 1) }}
        </a>
    @else
        <x-cta href="{{ route('login') }}" variant="outline">{{ __('nav.sign_in') }}</x-cta>
        <x-cta href="{{ route('register') }}" variant="dark">{{ __('nav.sign_up') }}</x-cta>
    @endauth
</div>
