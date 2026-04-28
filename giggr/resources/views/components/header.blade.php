<header class="sticky top-0 z-50 bg-bg border-b border-dark/10">
    <h1 class="sr-only">{{ __('nav.site_title') }}</h1>
    <nav class="max-w-6xl mx-auto px-6 py-4 flex items-center gap-10 relative"
         aria-label="{{ __('nav.aria_main_nav') }}">
        <h2 class="sr-only">{{ __('nav.aria_main_nav') }}</h2>

        <x-logo />
        <x-nav.links />

        <div class="ml-auto flex items-center gap-3">
            <x-nav.actions />
            <x-nav.mobile-menu />
        </div>

    </nav>
</header>
