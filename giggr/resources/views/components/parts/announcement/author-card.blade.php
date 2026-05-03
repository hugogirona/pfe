@props(['author', 'name'])

@php
    $image = $author->avatar_path;
    $cityName = $author->city?->name;
    $instruments = $author->instruments->pluck('name');
    $genres = $author->genres->pluck('name');
@endphp

<div class="bg-white rounded-2xl border border-dark/10 shadow-sm overflow-hidden">

    {{-- Header label --}}
    <div class="px-6 pt-6 pb-4 border-b border-dark/[0.07]">
        <p class="text-[11px] font-semibold uppercase tracking-widest text-dark/35">
            {{ __('announcement.author_title') }}
        </p>
    </div>

    {{-- Identity --}}
    <div class="px-6 py-5 flex items-center gap-4 border-b border-dark/[0.07]">
        <div class="shrink-0 w-14 h-14 rounded-full overflow-hidden bg-pastel-blue ring-2 ring-bg shadow-sm">
            @if ($image)
                <img
                    src="{{ Vite::asset('resources/img/profiles/' . $image) }}"
                    alt="{{ __('profile.avatar_alt', ['name' => $name]) }}"
                    class="w-full h-full object-cover"
                />
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <span class="font-heading text-xl text-dark/30 select-none">{{ mb_substr($name, 0, 1) }}</span>
                </div>
            @endif
        </div>

        <div class="min-w-0">
            <p class="font-heading text-xl text-dark leading-tight truncate">{{ $name }}</p>
            <p class="text-sm text-dark/45 mt-0.5 flex items-center gap-1">
                <x-icon name="map-pin" class="w-3 h-3 shrink-0" />
                {{ $cityName }}
            </p>
        </div>
    </div>

    {{-- Instrument & genre tags --}}
    @if ($instruments->isNotEmpty())
        <div class="px-6 py-4 border-b border-dark/[0.07]">
            <div class="flex flex-wrap gap-1.5">
                @foreach ($instruments as $instr)
                    <x-pill variant="instrument">{{ $instr }}</x-pill>
                @endforeach
                @foreach ($genres as $genre)
                    <x-pill variant="genre">{{ $genre }}</x-pill>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Actions --}}
    <div class="px-6 py-5 space-y-2.5">
        <x-cta variant="accent" class="w-full gap-2">
            <x-icon name="chat-bubble" class="w-4 h-4" />
            {{ __('announcement.author_contact', ['name' => $name]) }}
        </x-cta>

        <a
            href="{{ route('profile', ['id' => $author->id]) }}"
            class="group w-full flex items-center justify-center gap-1.5 py-2 text-sm font-medium text-dark/55 hover:text-dark transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-dark/20 rounded-lg"
        >
            {{ __('announcement.author_see_profile') }}
            <x-icon name="arrow-right" class="w-3.5 h-3.5 motion-safe:transition-transform motion-safe:duration-150 group-hover:translate-x-0.5" />
        </a>
    </div>

</div>
