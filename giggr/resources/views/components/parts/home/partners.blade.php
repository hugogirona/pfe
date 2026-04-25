@php
$logos = [
    ['file' => 'apple-music-logo.svg', 'alt' => 'Logo Apple Music'],
    ['file' => 'soundcloud-logo.svg',  'alt' => 'Logo SoundCloud'],
    ['file' => 'spotify-logo.svg',     'alt' => 'Logo Spotify'],
    ['file' => 'gibson-logo.svg',      'alt' => 'Logo Gibson'],
    ['file' => 'fender-logo.svg',      'alt' => 'Logo Fender'],
    ['file' => 'deezer-logo.svg',      'alt' => 'Logo Deezer'],
];
@endphp

<section class=" py-12 md:py-20">

    <h2 class="text-center font-heading text-sm tracking-[0.3em] uppercase text-dark/40 mb-14 mx-6">
        Ils nous font confiance
    </h2>

    <div class="max-w-6xl mx-auto">
    <div class="relative h-8 overflow-hidden marquee-mask"
         x-data="{ paused: false }"
         @mouseenter="paused = true"
         @mouseleave="paused = false">

        <div class="absolute top-0 left-0 flex w-max gap-20 items-center animate-marquee pr-20"
             :style="paused ? 'animation-play-state: paused' : ''">

            {{-- Série 1 --}}
            @foreach ($logos as $logo)
                <img
                    src="{{ Vite::asset('resources/img/partners/' . $logo['file']) }}"
                    alt="{{ $logo['alt'] }}"
                    class="h-8 w-auto grayscale opacity-40 hover:grayscale-0 hover:opacity-100 transition-all duration-300 cursor-pointer"
                />
            @endforeach

            {{-- Série 2 --}}
            @foreach ($logos as $logo)
                <img
                    src="{{ Vite::asset('resources/img/partners/' . $logo['file']) }}"
                    alt="{{ $logo['alt'] }}"
                    aria-hidden="true"
                    class="h-8 w-auto grayscale opacity-40 hover:grayscale-0 hover:opacity-100 transition-all duration-300 cursor-pointer"
                />
            @endforeach

            {{-- Série 3 --}}
            @foreach ($logos as $logo)
                <img
                    src="{{ Vite::asset('resources/img/partners/' . $logo['file']) }}"
                    alt="{{ $logo['alt'] }}"
                    aria-hidden="true"
                    class="h-8 w-auto grayscale opacity-40 hover:grayscale-0 hover:opacity-100 transition-all duration-300 cursor-pointer"
                />
            @endforeach

        </div>
    </div>
    </div>

</section>
