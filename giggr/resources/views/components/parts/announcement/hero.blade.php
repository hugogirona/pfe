@props(['announcement'])

@php
$typeColors = [
    'Recherche'  => 'bg-accent text-bg',
    'Formation'  => 'bg-dark text-bg',
    'Session'    => 'bg-pastel-blue text-dark',
    'Cours'      => 'bg-pastel-salmon text-dark',
    'Événement'  => 'bg-pastel-taupe text-dark',
];
$badgeClass = $typeColors[$announcement['type']] ?? 'bg-dark/10 text-dark';
@endphp

<div class="relative bg-dark overflow-hidden" aria-hidden="false">

    {{-- Gradient overlay --}}
    <div class="absolute inset-0 bg-gradient-to-br from-dark via-dark to-accent/30"></div>

    {{-- Decorative circles --}}
    <div class="absolute -bottom-16 -right-16 w-80 h-80 rounded-full bg-accent/10 blur-2xl"></div>
    <div class="absolute top-0 left-1/2 w-64 h-64 rounded-full bg-accent/5 blur-xl"></div>

    {{-- Grid pattern --}}
    <div class="absolute inset-0 opacity-[0.04]"
         style="background-image: repeating-linear-gradient(0deg, currentColor, currentColor 1px, transparent 1px, transparent 40px), repeating-linear-gradient(90deg, currentColor, currentColor 1px, transparent 1px, transparent 40px); color: white;"></div>

    <div class="relative z-10 max-w-6xl mx-auto px-6 py-8 md:py-12">

        {{-- Back link --}}
        <a
            href="{{ route('explore') }}?tab=annonces"
            class="inline-flex items-center gap-1.5 text-sm text-bg/60 hover:text-bg transition-colors duration-150 mb-6 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-bg/50 rounded"
        >
            {{ __('announcement.back_to_explore') }}
        </a>

        {{-- Type badge + meta --}}
        <div class="flex flex-wrap items-center gap-3 mb-4">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide {{ $badgeClass }}">
                {{ $announcement['type'] }}
            </span>
            <span class="flex items-center gap-1.5 text-sm text-bg/50">
                <x-icon name="map-pin" class="w-3.5 h-3.5" />
                {{ $announcement['city'] }}
            </span>
            <span class="flex items-center gap-1.5 text-sm text-bg/50">
                <x-icon name="calendar" class="w-3.5 h-3.5" />
                {{ __('announcement.posted_on', ['date' => $announcement['date']]) }}
            </span>
        </div>

        {{-- Title --}}
        <h1 class="font-heading text-3xl md:text-5xl text-bg leading-tight max-w-3xl">
            {{ $announcement['title'] }}
        </h1>

    </div>
</div>
