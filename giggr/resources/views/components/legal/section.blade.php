@props([
    'heading',
])

<section class="space-y-3">
    <h2 class="font-heading text-2xl md:text-3xl text-heading">{{ $heading }}</h2>
    <div class="text-subtle text-base leading-relaxed space-y-3">
        {{ $slot }}
    </div>
</section>
