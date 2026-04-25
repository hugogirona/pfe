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

@php $isLeft = $orientation === 'left'; @endphp

<section class="max-w-6xl mx-auto px-6 py-16">
    <div @class([
        'flex rounded-3xl overflow-hidden shadow-sm',
        'flex-col md:flex-row'         => $isLeft,
        'flex-col md:flex-row-reverse' => !$isLeft,
    ])>

        <figure class="relative h-64 md:h-auto md:w-1/2 shrink-0">
            <img
                src="{{ Vite::asset('resources/img/' . $image) }}"
                alt="{{ $alt }}"
                class="absolute inset-0 w-full h-full object-cover"
            />
            <figcaption class="sro">{{ $alt }}</figcaption>
        </figure>

        <div @class([
            'flex-1 flex flex-col justify-center gap-6 px-10 py-14',
            $bg,
        ])>
            <div class="w-8 h-0.5 bg-accent rounded-full"></div>

            <h2 class="font-heading text-3xl md:text-4xl leading-tight text-dark">
                {{ $title }}
            </h2>

            @if ($content)
                <p class="text-sm leading-relaxed text-dark/60 max-w-sm">
                    {{ $content }}
                </p>
            @endif

            @if ($buttonLabel)
                <div>
                    <x-cta variant="dark" :href="$url">{{ $buttonLabel }}</x-cta>
                </div>
            @endif
        </div>

    </div>
</section>
