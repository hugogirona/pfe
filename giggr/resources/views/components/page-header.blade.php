@props(['title', 'subtitle' => null])

<div class="bg-dark text-bg py-14 md:py-20">
    <div class="max-w-6xl mx-auto px-6">
        <h1 class="font-heading text-4xl md:text-6xl mb-3">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-bg/60 text-lg">{{ $subtitle }}</p>
        @endif
    </div>
</div>
