@props([
    'bg' => 'bg-pastel-blue',
    'rotate' => 0,
    'icon' => '',
    'title' => '',
    'index' => 0,
])

<article
    data-stack-card
    data-rot="{{ $rotate }}"
    style="--card-index: {{ $index }}"
    {{ $attributes->class([
        'feature-stack-card mx-auto w-full max-w-sm md:max-w-md',
        'rounded-3xl shadow-xl shadow-dark/10 p-6 md:p-8',
        $bg,
    ]) }}
>
    <div class="flex items-center justify-center w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-dark/[0.06] mb-5 md:mb-6">
        <x-icon :name="$icon" class="w-7 h-7 md:w-8 md:h-8 text-subtle" />
    </div>

    <h3 class="font-heading text-2xl md:text-3xl text-heading mb-2 md:mb-3">{{ $title }}</h3>
    <p class="text-sm md:text-lg text-subtle leading-relaxed">{{ $slot }}</p>
</article>
