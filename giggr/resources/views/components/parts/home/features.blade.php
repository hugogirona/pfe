<section data-stack class="bg-bg" aria-labelledby="features-title">
    <div data-stack-pin class="flex flex-col py-12 md:py-24">

        <div data-stack-head class="shrink-0 max-w-2xl mx-auto px-6 text-center my-8 md:mb-14">
            <p class="inline-flex items-center gap-3 text-accent text-xs md:text-sm font-medium tracking-[0.3em] uppercase mb-4 md:mb-5">
                <span class="w-6 md:w-8 h-px bg-accent"></span>
                {{ __('home.features_eyebrow') }}
                <span class="w-6 md:w-8 h-px bg-accent"></span>
            </p>
            <h2 id="features-title" class="font-heading text-[1.75rem] leading-tight md:text-5xl md:leading-[1.1] text-heading">
                {{ __('home.features_title') }}
            </h2>
        </div>

        <div class="feature-stack flex-1 w-full max-w-sm md:max-w-md mx-auto px-6">
            <x-parts.home.feature-card :index="0" bg="bg-pastel-salmon" :rotate="-3" icon="users" :title="__('home.feature_community_title')">
                {{ __('home.feature_community_desc') }}
            </x-parts.home.feature-card>

            <x-parts.home.feature-card :index="1" bg="bg-pastel-blue" :rotate="-1.5" icon="plus" :title="__('home.feature_post_title')">
                {{ __('home.feature_post_desc') }}
            </x-parts.home.feature-card>

            <x-parts.home.feature-card :index="2" bg="bg-pastel-taupe" :rotate="0" icon="search" :title="__('home.feature_find_title')">
                {{ __('home.feature_find_desc') }}
            </x-parts.home.feature-card>
        </div>

    </div>

</section>
