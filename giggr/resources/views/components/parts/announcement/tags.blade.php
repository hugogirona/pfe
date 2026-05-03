@props(['announcement'])

@php
    $instruments = $announcement->instruments->pluck('name');
    $genres = $announcement->genres->pluck('name');
@endphp

@if ($instruments->isNotEmpty() || $genres->isNotEmpty())
    <section aria-labelledby="tags-heading" class="bg-white rounded-2xl border border-dark/10 shadow-sm p-6 md:p-8">

        <h2 id="tags-heading" class="font-heading text-2xl text-dark mb-5">
            {{ __('announcement.tags_title') }}
        </h2>

        <div class="space-y-4">

            @if ($instruments->isNotEmpty())
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-dark/35 mb-2.5">
                        <x-icon name="music-note" class="w-3 h-3 inline mr-1" />
                        Instruments
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($instruments as $instr)
                            <x-pill variant="instrument" size="lg">{{ $instr }}</x-pill>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($genres->isNotEmpty())
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-dark/35 mb-2.5">Genres</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($genres as $genre)
                            <x-pill variant="genre" size="lg">{{ $genre }}</x-pill>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </section>
@endif
