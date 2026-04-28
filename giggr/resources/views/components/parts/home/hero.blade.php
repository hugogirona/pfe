<section class="max-w-6xl mx-auto px-6 py-12 md:py-24 flex flex-col md:flex-row items-center gap-12 md:gap-8">

    <div class="flex-1 flex flex-col items-start">
        <p class="text-base font-medium text-dark/50">
            {{ __('home.welcome') }} <span class="text-accent">Giggr</span>
        </p>

        <h2 class="font-heading text-4xl md:text-5xl leading-[1.1] mt-5 text-dark max-w-lg">
            {{ __('home.hero_title') }}
        </h2>

        <p class="mt-6 text-base md:text-lg leading-relaxed text-dark/55 max-w-sm">
            {{ __('home.hero_subtitle') }}
        </p>
        
        {{-- //TODO insert accent svg subline --}}

        <form class="mt-8 flex flex-col sm:flex-row gap-2 sm:gap-3 w-full max-w-lg" role="search">
            <x-search-bar class="min-w-0" />
            <x-cta variant="dark" size="lg" href="#" class="shrink-0 w-full sm:w-auto">{{ __('home.hero_cta') }}</x-cta>
        </form>
    </div>

    <div class="flex-1 flex justify-center md:justify-end">
        <img
            src="{{ Vite::asset('resources/img/hero-guy.svg') }}"
            alt="Musicien entouré d'instruments de musique"
            class="w-full max-w-md md:max-w-lg"
            width="723"
            height="636"
        />
    </div>

</section>
