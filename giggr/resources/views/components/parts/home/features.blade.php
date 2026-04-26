<section class="relative bg-dark py-12 md:py-20 overflow-hidden">

    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
    <div class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-[radial-gradient(ellipse_80%_50%_at_50%_0%,rgba(246,118,73,0.07),transparent)]"></div>

    <div class="relative max-w-6xl mx-auto px-6">

        <div class="text-center mb-12">
            <p class="inline-flex items-center gap-3 text-accent text-base font-medium tracking-[0.3em] uppercase mb-5">
                <span class="w-8 h-px bg-accent"></span>
                {{ __('home.features_eyebrow') }}
                <span class="w-8 h-px bg-accent"></span>
            </p>
            <h2 class="font-heading text-3xl md:text-4xl text-white leading-tight">
                {{ __('home.features_title') }}
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <x-parts.home.feature-card icon="search" :title="__('home.feature_find_title')">
                {{ __('home.feature_find_desc') }}
            </x-parts.home.feature-card>

            <x-parts.home.feature-card icon="plus-circle" :title="__('home.feature_post_title')">
                {{ __('home.feature_post_desc') }}
            </x-parts.home.feature-card>

            <x-parts.home.feature-card icon="users" :title="__('home.feature_community_title')">
                {{ __('home.feature_community_desc') }}
            </x-parts.home.feature-card>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <x-parts.home.feature-kpi value="12" :label="__('home.kpi_musicians')" />
            <x-parts.home.feature-kpi value="3" :label="__('home.kpi_ads')" />

            <div class="col-span-2 bg-accent/10 border border-accent/20 rounded-2xl py-7 px-6 flex flex-col items-center justify-center text-center gap-4">
                <p class="text-base text-white/65 leading-snug">{{ __('home.cta_join') }}</p>
                <x-cta variant="accent" href="#">{{ __('nav.sign_up') }}</x-cta>
            </div>
        </div>

    </div>

    <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>

</section>
