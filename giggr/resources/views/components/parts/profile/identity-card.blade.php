@props(['musician'])

<div class="bg-white rounded-2xl border border-dark/10 shadow-sm overflow-hidden">

    {{-- Avatar --}}
    <div class="flex justify-center pt-8 pb-4 px-6">
        <div class="relative">
            <div class="w-28 h-28 rounded-full overflow-hidden bg-pastel-blue ring-4 ring-bg shadow-md">
                @if (!empty($musician['image']))
                    <img
                        src="{{ Vite::asset('resources/img/profiles/' . $musician['image']) }}"
                        alt="{{ __('profile.avatar_alt', ['name' => $musician['name']]) }}"
                        class="w-full h-full object-cover object-center"
                    />
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <span class="font-heading text-4xl text-dark/30 select-none">
                            {{ mb_substr($musician['name'], 0, 1) }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Status dot --}}
            <span class="absolute bottom-1 right-1 w-4 h-4 rounded-full bg-accent ring-2 ring-bg" aria-hidden="true"></span>
        </div>
    </div>

    {{-- Identity --}}
    <div class="text-center px-6 pb-5 border-b border-dark/[0.07]">
        <h2 class="font-heading text-2xl text-dark leading-tight">{{ $musician['name'] }}</h2>
        <p class="text-sm text-dark/50 mt-1 flex items-center justify-center gap-1">
            <x-icon name="map-pin" class="w-3.5 h-3.5" />
            {{ $musician['city'] }}
        </p>

        @if (!empty($musician['status']))
            <span class="inline-flex mt-3 px-3 py-1 rounded-full bg-dark text-bg text-xs font-semibold tracking-wide">
                {{ $musician['status'] }}
            </span>
        @endif
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 divide-x divide-dark/[0.07] border-b border-dark/[0.07]">
        <div class="flex flex-col items-center py-4 px-3">
            <span class="font-heading text-2xl text-dark">{{ $musician['experience'] }}</span>
            <span class="text-[11px] text-dark/45 text-center leading-tight mt-0.5">{{ __('profile.stat_experience') }}</span>
        </div>
        <div class="flex flex-col items-center py-4 px-3">
            <span class="font-heading text-2xl text-dark">{{ $musician['active_ads'] }}</span>
            <span class="text-[11px] text-dark/45 text-center leading-tight mt-0.5">
                {{ trans_choice('profile.stat_ads', $musician['active_ads']) }}
            </span>
        </div>
    </div>

    {{-- Instruments --}}
    @if (!empty($musician['instruments']))
        <div class="px-6 pt-5 pb-3">
            <p class="text-[11px] font-semibold uppercase tracking-widest text-dark/35 mb-2.5">Instruments
            </p>
            <div class="flex flex-wrap gap-1.5">
                @foreach ($musician['instruments'] as $instr)
                    <span class="px-2.5 py-1 rounded-full bg-pastel-salmon text-xs font-medium text-accent">
                        {{ $instr }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Genres --}}
    @if (!empty($musician['genres']))
        <div class="px-6 pb-5">
            <p class="text-[11px] font-semibold uppercase tracking-widest text-dark/35 mb-2.5">Genres</p>
            <div class="flex flex-wrap gap-1.5">
                @foreach ($musician['genres'] as $genre)
                    <span class="px-2.5 py-1 rounded-full bg-dark/[0.06] text-xs font-medium text-dark/60">
                        {{ $genre }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Actions --}}
    <div class="px-6 pb-6 border-t border-dark/[0.07] pt-5">
        <x-parts.profile.social-actions :musician="$musician" />
    </div>

</div>
