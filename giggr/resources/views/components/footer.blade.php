<footer class="bg-dark text-on-dark" itemscope itemtype="https://schema.org/Organization">
    <meta itemprop="name" content="{{ config('app.name') }}">
    <link itemprop="url" href="{{ url('/') }}">
    <meta itemprop="logo" content="{{ Vite::asset('resources/favicon/web-app-manifest-512x512.png') }}">
    <div class="max-w-6xl mx-auto px-6 py-12">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8">

            <div class="flex flex-col items-center md:items-start">
                <x-logo class="text-on-dark" />
                <p class="mt-3 text-base text-on-dark-subtle max-w-xs text-center md:text-left" itemprop="description">
                    {{ __('footer.tagline') }}
                </p>
            </div>

            <div class="md:ml-auto flex flex-col md:flex-row items-center md:items-start gap-8">
                <nav class="flex flex-col gap-3 items-center md:items-start" aria-labelledby="footer-nav-heading">
                    <h2 id="footer-nav-heading" class="sr-only">{{ __('footer.nav_aria') }}</h2>
                    <a href="{{ route('home') }}" wire:navigate.hover class="text-base text-on-dark-subtle hover:text-on-dark transition-colors duration-150 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent-on-dark rounded-sm">{{ __('footer.home') }}</a>
                    <a href="{{ route('explore') }}" wire:navigate.hover class="text-base text-on-dark-subtle hover:text-on-dark transition-colors duration-150 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent-on-dark rounded-sm">{{ __('footer.explore') }}</a>
                    <a href="{{ route('contact') }}" wire:navigate.hover class="text-base text-on-dark-subtle hover:text-on-dark transition-colors duration-150 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent-on-dark rounded-sm">{{ __('footer.contact') }}</a>
                    <a href="{{ route('privacy') }}" wire:navigate.hover class="text-base text-on-dark-subtle hover:text-on-dark transition-colors duration-150 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent-on-dark rounded-sm">{{ __('footer.privacy') }}</a>
                </nav>

                <x-footer.socials />
            </div>

        </div>
        <div class="mt-12 pt-6 border-t border-bg/10 flex items-start justify-between">
            <span class="text-xs text-on-dark-caption">{{ __('footer.copyright', ['year' => date('Y')]) }}</span>
            <x-footer.lang-switcher />
        </div>
    </div>
</footer>
