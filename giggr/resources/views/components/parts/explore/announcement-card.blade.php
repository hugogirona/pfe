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

<article
    data-card="announcement"
    class="flex flex-col w-full rounded-xl overflow-hidden border border-dark/10 bg-white shadow-sm hover:shadow-md transition-shadow duration-200 cursor-pointer"
    tabindex="0"
>
    {{-- Header band --}}
    <div class="px-5 pt-5 pb-4 border-b border-dark/[0.07]">
        <div class="flex items-start justify-between gap-3">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide {{ $badgeClass }}">
                {{ $announcement['type'] }}
            </span>
            <span class="text-xs text-dark/35 shrink-0 mt-0.5">{{ $announcement['city'] }}</span>
        </div>
        <h3 class="font-heading text-xl text-dark mt-3 leading-snug">{{ $announcement['title'] }}</h3>
    </div>

    {{-- Content --}}
    <div class="flex-1 px-5 py-4 space-y-3">
        <p class="text-sm text-dark/55 leading-relaxed line-clamp-3">{{ $announcement['description'] }}</p>

        {{-- Tags --}}
        <div class="flex flex-wrap gap-1.5">
            @foreach ($announcement['instruments'] as $instr)
                <span class="px-2.5 py-0.5 rounded-full bg-dark/[0.06] text-xs font-medium text-dark/60">{{ $instr }}</span>
            @endforeach
            @foreach ($announcement['genres'] as $genre)
                <span class="px-2.5 py-0.5 rounded-full bg-pastel-salmon text-xs font-medium text-dark/60">{{ $genre }}</span>
            @endforeach
        </div>
    </div>

    {{-- Footer --}}
    <div class="px-5 pb-4 flex items-center justify-between gap-4">
        <span class="text-xs text-dark/35">
            {{ __('explore.card_posted_on', ['date' => $announcement['date']]) }}
        </span>
        <a
            href="{{ route('announcement', ['id' => $announcement['id']]) }}"
            class="group inline-flex items-center gap-1.5 text-sm font-medium text-dark/60 hover:text-accent transition-colors duration-150 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded"
        >
            {{ __('explore.card_see_announcement') }}
            <x-icon name="arrow-right" class="w-3.5 h-3.5 motion-safe:transition-transform motion-safe:duration-150 group-hover:translate-x-0.5" />
        </a>
    </div>

</article>
