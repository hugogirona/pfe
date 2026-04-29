<footer class="bg-dark text-bg">
    <div class="max-w-6xl mx-auto px-6 py-12">
        <div class="flex flex-col md:flex-row items-center gap-8">

            <div class="flex flex-col items-center md:items-start">
                <x-logo class="text-bg" />
                <p class="mt-3 text-base text-bg/60 max-w-xs text-center md:text-left">
                    {{ __('footer.tagline') }}
                </p>
            </div>

            <div class="md:ml-auto flex flex-col md:flex-row items-center md:items-start gap-8">
                <nav class="flex flex-col gap-3 items-center md:items-start">
                    <h2 class="sr-only">{{ __('footer.nav_aria') }}</h2>
                    <a href="{{ route('home') }}" wire:navigate.hover class="text-base text-bg/70 hover:text-bg transition-colors duration-150">{{ __('footer.home') }}</a>
                    <a href="{{ route('explore') }}" wire:navigate.hover class="text-base text-bg/70 hover:text-bg transition-colors duration-150">{{ __('footer.explore') }}</a>
                    <a href="{{ route('contact') }}" wire:navigate.hover class="text-base text-bg/70 hover:text-bg transition-colors duration-150">{{ __('footer.contact') }}</a>
                </nav>

                <x-footer.socials />
            </div>

        </div>
        <div class="mt-12 pt-6 border-t border-bg/10 flex items-center justify-between">
            <span class="text-xs text-bg/40">{{ __('footer.copyright', ['year' => date('Y')]) }}</span>
            <x-footer.lang-switcher />
        </div>
    </div>
</footer>
