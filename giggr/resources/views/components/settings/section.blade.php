@props([
    'title',
    'description' => null,
    'labelledby',
])

<section
    aria-labelledby="{{ $labelledby }}"
    {{ $attributes->class('bg-white rounded-2xl border border-dark/10 shadow-sm p-6 md:p-8') }}
>
    <header class="mb-5">
        <h2 id="{{ $labelledby }}" class="font-heading text-xl text-dark">{{ $title }}</h2>
        @if ($description)
            <p class="text-sm text-dark/55 mt-1.5 leading-relaxed">{{ $description }}</p>
        @endif
    </header>

    {{ $slot }}
</section>
