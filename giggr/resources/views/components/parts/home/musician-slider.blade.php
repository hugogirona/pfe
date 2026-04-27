@php
$profiles = [
    ['name' => 'Valentine', 'age' => 26, 'city' => 'Liège, Belgique',    'bio' => 'Cherche groupe de rock pour projet sérieux. 8 ans d\'expérience, disponible les weekends.',                              'image' => 'valentine.webp', 'url' => '#'],
    ['name' => 'Thomas',    'age' => 30, 'city' => 'Namur, Belgique',    'bio' => 'Batteur jazz & funk depuis 12 ans. Disponible en soirée et les weekends pour projets studio ou scène.',              'image' => 'thomas.webp',    'url' => '#'],
    ['name' => 'Sarah',     'age' => 22, 'city' => 'Liège, Belgique',    'bio' => 'Pianiste classique reconvertie jazz. Je cherche des musiciens motivés pour des jams régulières.',                    'image' => 'sarah.webp',     'url' => '#'],
    ['name' => 'Maxime',    'age' => 28, 'city' => 'Bruxelles, Belgique','bio' => 'Bassiste funk & soul, 10 ans de scène. Cherche groupe pour concerts et enregistrements studio.',                    'image' => 'maxime.webp',    'url' => '#'],
    ['name' => 'Lucie',     'age' => 24, 'city' => 'Gand, Belgique',     'bio' => 'Chanteuse pop-indie à la recherche d\'un groupe créatif. Compositrice, ouverte à tous styles.',                     'image' => 'lucie.webp',     'url' => '#'],
    ['name' => 'Antoine',   'age' => 32, 'city' => 'Liège, Belgique',    'bio' => 'Saxophoniste depuis 15 ans, jazz et improvisation. Dispo pour projets live et jam sessions.',                        'image' => 'antoine.webp',   'url' => '#'],
    ['name' => 'Inès',      'age' => 25, 'city' => 'Namur, Belgique',    'bio' => 'Violoniste classique qui explore le folk et le post-rock. Cherche musiciens pour projets originaux.',               'image' => 'ines.webp',      'url' => '#'],
];

$cloneCount  = 3;
$prepended   = array_slice($profiles, -$cloneCount);
$appended    = array_slice($profiles,  0, $cloneCount);
$cardClass   = 'snap-center shrink-0 flex flex-col min-w-[250px] w-[85%] sm:w-[calc(50%-12px)] md:w-[calc(33.333%-16px)]';
@endphp

<section class="py-16 md:py-20 bg-pastel-taupe">
    <div class="max-w-6xl mx-auto px-6">

        <div class="text-center mb-10">
            <h2 class="font-heading text-4xl md:text-5xl text-dark">{{ __('home.musicians_title') }}</h2>
            <p class="mt-4 text-base md:text-lg text-dark/55">{{ __('home.musicians_subtitle') }}</p>
        </div>

        <div
            class="relative"
            x-data="musiciansSlider({{ count($profiles) }}, {{ $cloneCount }})"
        >
            <button
                @click="prev()"
                class="hidden md:flex absolute -left-5 top-[calc(50%-2rem)] -translate-y-1/2 z-10 w-11 h-11 items-center justify-center rounded-full bg-bg border border-dark/15 shadow-sm text-dark hover:bg-dark hover:text-bg transition-colors duration-200 cursor-pointer"
                aria-label="{{ __('home.carousel_prev') }}"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </button>

            {{-- Track --}}
            <div
                x-ref="track"
                @scroll.passive="onScroll()"
                class="flex gap-6 overflow-x-auto snap-x snap-mandatory scroll-smooth scrollbar-none overscroll-x-contain pb-2"
            >
                {{-- Clones gauche (dernières 3 cards) --}}
                @foreach ($prepended as $profile)
                    <div class="{{ $cardClass }}" aria-hidden="true">
                        <x-musician-card :musician="$profile" />
                    </div>
                @endforeach

                {{-- Cards réelles --}}
                @foreach ($profiles as $profile)
                    <div class="{{ $cardClass }}">
                        <x-musician-card :musician="$profile" />
                    </div>
                @endforeach

                {{-- Clones droite (premières 3 cards) --}}
                @foreach ($appended as $profile)
                    <div class="{{ $cardClass }}" aria-hidden="true">
                        <x-musician-card :musician="$profile" />
                    </div>
                @endforeach
            </div>

            {{-- Flèche droite --}}
            <button
                @click="next()"
                class="hidden md:flex absolute -right-5 top-[calc(50%-2rem)] -translate-y-1/2 z-10 w-11 h-11 items-center justify-center rounded-full bg-bg border border-dark/15 shadow-sm text-dark hover:bg-dark hover:text-bg transition-colors duration-200 cursor-pointer"
                aria-label="{{ __('home.carousel_next') }}"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </button>

            {{-- Dots --}}
            <div class="flex justify-center items-center gap-2 mt-8" role="tablist" aria-label="{{ __('home.carousel_aria') }}">
                <template x-for="i in real" :key="i">
                    <button
                        @click="goToReal(i - 1)"
                        :aria-label="`{{ __('home.carousel_goto') }}${i}`"
                        :aria-selected="current === i - 1"
                        class="h-2 rounded-full transition-all duration-300 cursor-pointer motion-reduce:transition-none"
                        :class="current === i - 1 ? 'w-6 bg-accent' : 'w-2 bg-dark/25 hover:bg-dark/45'"
                    ></button>
                </template>
            </div>

        </div>

    </div>
</section>
