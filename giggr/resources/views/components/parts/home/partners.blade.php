@php
$logos = [
    ['file' => 'apple-music-logo.svg', 'alt' => 'Logo Apple Music', 'w' => 151, 'h' => 37],
    ['file' => 'soundcloud-logo.svg',  'alt' => 'Logo SoundCloud',  'w' => 75,  'h' => 43],
    ['file' => 'spotify-logo.svg',     'alt' => 'Logo Spotify',     'w' => 140, 'h' => 48],
    ['file' => 'gibson-logo.svg',      'alt' => 'Logo Gibson',      'w' => 77,  'h' => 49],
    ['file' => 'fender-logo.svg',      'alt' => 'Logo Fender',      'w' => 143, 'h' => 54],
    ['file' => 'deezer-logo.svg',      'alt' => 'Logo Deezer',      'w' => 51,  'h' => 50],
];
@endphp

<section class="py-12 md:py-24">

    <h2 class="text-center font-heading text-base tracking-[0.3em] uppercase text-caption mb-14 mx-6">
        {{ __('home.partners_title') }}
    </h2>

    <div class="max-w-6xl mx-auto">
        <div class="relative h-8 overflow-hidden marquee-mask"
             x-data="{ paused: false }"
             @mouseenter="paused = true"
             @mouseleave="paused = false">

            <div class="absolute top-0 left-0 flex w-max gap-20 items-center animate-marquee pr-20"
                 :style="paused ? 'animation-play-state: paused' : ''">

                @for ($series = 0; $series < 3; $series++)
                    @foreach ($logos as $logo)
                        <img
                            src="{{ Vite::asset('resources/img/partners/' . $logo['file']) }}"
                            alt="{{ $logo['alt'] }}"
                            width="{{ $logo['w'] }}"
                            height="{{ $logo['h'] }}"
                            loading="lazy"
                            decoding="async"
                            @if ($series > 0) aria-hidden="true" @endif
                            class="h-8 w-auto grayscale opacity-40 hover:grayscale-0 hover:opacity-100 transition-all duration-300 cursor-pointer"
                        />
                    @endforeach
                @endfor

            </div>
        </div>
    </div>

</section>
