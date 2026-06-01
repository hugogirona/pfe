@props([
    'bg' => 'bg-pastel-blue',
    'rotate' => 0,
    'icon' => '',
    'title' => '',
    'index' => 0,
])

{{-- Without JS the cards are a plain readable vertical list. When GSAP runs it
     adds .is-stacking, which switches them to the absolute, tilted stack. --}}
<article
    data-stack-card
    data-rot="{{ $rotate }}"
    style="--card-index: {{ $index }}"
    {{ $attributes->class([
        'feature-stack-card mx-auto w-full max-w-sm md:max-w-md',
        'rounded-3xl shadow-xl shadow-dark/10 p-6 md:p-10',
        $bg,
    ]) }}
>
    <div class="flex items-center justify-center w-14 h-14 md:w-20 md:h-20 rounded-2xl bg-dark/[0.06] mb-5 md:mb-8">
        <x-icon :name="$icon" class="w-7 h-7 md:w-10 md:h-10 text-dark/70" />
    </div>

    <h3 class="font-heading text-2xl md:text-4xl text-dark mb-2 md:mb-3">{{ $title }}</h3>
    <p class="text-sm md:text-lg text-dark/55 leading-relaxed">{{ $slot }}</p>
</article>
