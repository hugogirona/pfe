@props([
    'title' => '',
    'content' => '',
    'buttonLabel' => '',
    'url' => '#',
    'image' => '',
    'alt' => '',
    'width' => null,
    'height' => null,
    'srcsetWidths' => [],
    'orientation' => 'left',
    'bg' => 'bg-pastel-blue',
])

@php
    $isLeft = $orientation === 'left';
    $sectionId = 'ti-' . uniqid();

    $base = pathinfo($image, PATHINFO_FILENAME);
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $sources = collect($srcsetWidths)
        ->map(fn ($w) => Vite::asset("resources/img/{$base}-{$w}w.{$ext}") . " {$w}w");

    if ($width) {
        $sources->push(Vite::asset("resources/img/{$image}") . " {$width}w");
    }

    $srcset = $sources->implode(', ');
@endphp

<div class="max-w-6xl mx-auto px-6 py-12 md:py-24">
    <section @class([
        'flex rounded-3xl overflow-hidden shadow-sm',
        'flex-col-reverse md:flex-row' => !$isLeft,
        'flex-col-reverse md:flex-row-reverse' => $isLeft,
    ]) aria-labelledby="{{ $sectionId }}">

        <div @class([
            'flex-1 flex flex-col justify-center gap-6 px-10 py-14',
            $bg,
        ])>
            <div class="w-8 h-0.5 bg-accent rounded-full"></div>

            <h2 id="{{ $sectionId }}" class="font-heading text-3xl md:text-4xl leading-tight text-heading">
                {{ $title }}
            </h2>

            @if ($content)
                <p class="text-base md:text-lg leading-relaxed text-subtle max-w-sm">
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
                @if ($srcsetWidths)
                    srcset="{{ $srcset }}"
                sizes="(min-width: 768px) 50vw, 100vw"
                @endif
                alt="{{ $alt }}"
                @if ($width) width="{{ $width }}" @endif
                @if ($height) height="{{ $height }}" @endif
                loading="lazy"
                decoding="async"
                class="absolute inset-0 w-full h-full object-cover"
            />
            <figcaption class="sr-only">{{ $alt }}</figcaption>
        </figure>
    </section>
</div>
