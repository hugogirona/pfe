@props(['title', 'subtitle' => null])

<div class="bg-dark text-on-dark py-14 md:py-20">
    <div class="max-w-6xl mx-auto px-6">
        <p class="font-heading text-4xl md:text-6xl mb-3">{{ $title }}</p>
        @if($subtitle)
            <p class="text-on-dark-subtle text-lg">{{ $subtitle }}</p>
        @endif
    </div>
</div>
