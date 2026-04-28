@props([
    'title'       => '',
    'content'     => '',
    'buttonLabel' => '',
    'url'         => '#',
    'image'       => '',
    'alt'         => '',
    'orientation' => 'left',
    'bg'          => 'bg-pastel-blue',
])

@php
    $isLeft    = $orientation === 'left';
    $sectionId = 'ti-' . uniqid();
@endphp

<div class="max-w-6xl mx-auto px-6 py-12 md:py-24">
    <section @class([
        'flex rounded-3xl overflow-hidden shadow-sm',
        'flex-col md:flex-row'         => !$isLeft,
        'flex-col md:flex-row-reverse' => $isLeft,
    ]) aria-labelledby="{{ $sectionId }}">

        <div @class([
            'flex-1 flex flex-col justify-center gap-6 px-10 py-14',
            $bg,
        ])>
            <div class="w-8 h-0.5 bg-accent rounded-full"></div> {{--TDOO: maybe mettre un point pour rappeler le logo--}}

            <h2 id="{{ $sectionId }}" class="font-heading text-3xl md:text-4xl leading-tight text-dark">
                {{ $title }}
            </h2>

            @if ($content)
                <p class="text-base md:text-lg leading-relaxed text-dark/60 max-w-sm">
                    {{ $content }}
                </p>
            @endif

            @if ($buttonLabel)
                <div>
                    <x-cta size="lg" variant="dark" :href="$url">{{ $buttonLabel }}</x-cta>
                </div>
            @endif
        </div>
        <figure class="relative h-64 md:h-auto md:w-1/2 shrink-0">
            <img
                src="{{ Vite::asset('resources/img/' . $image) }}"
                alt="{{ $alt }}"
                class="absolute inset-0 w-full h-full object-cover"
            />
            <figcaption class="sr-only">{{ $alt }}</figcaption>
        </figure>
    </section>
</div>
