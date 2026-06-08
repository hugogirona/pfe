@props(['announcement'])

@php
    $isOwner           = auth()->check() && auth()->id() === $announcement->user_id;
    $pillLimit         = 2;
    $shownInstruments  = $announcement->instruments->take($pillLimit);
    $extraInstruments  = max(0, $announcement->instruments->count() - $pillLimit);
    $shownGenres       = $announcement->genres->take($pillLimit);
    $extraGenres       = max(0, $announcement->genres->count() - $pillLimit);
@endphp

<div {{ $attributes->class('relative h-full') }} itemscope itemtype="https://schema.org/Event">
    <link itemprop="url" href="{{ route('announcement', ['id' => $announcement->id]) }}">
    @if ($isOwner)
        <x-cta
            variant="simple"
            size="icon"
            class="absolute top-2 right-2 z-20"
            @click="Livewire.dispatch('open-modal', { component: 'parts.announcement.form', title: {{ json_encode(__('announcement.edit_title')) }}, model_id: '{{ $announcement->id }}' })"
            aria-label="{{ __('announcement.edit_aria') }}"
        >
            <x-icon name="pencil-square" class="w-4 h-4"/>
        </x-cta>
    @endif

    <a
        href="{{ route('announcement', ['id' => $announcement->id]) }}"
        @guest x-data @click.prevent="$dispatch('open-auth-modal')" @endguest
        @auth wire:navigate @endauth
        class="block h-full focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-xl"
    >
        <article
            class="group flex flex-col w-full h-full rounded-xl overflow-hidden border border-dark/10 bg-white shadow-sm hover:shadow-md transition-shadow duration-200"
        >
            {{-- Header --}}
            <div class="px-6 pt-6 pb-5 border-b border-dark/[0.07]">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide {{ $announcement->type->color() }}">
                    {{ __($announcement->type->label()) }}
                </span>
                <h3 @class([
                    'font-heading text-xl text-heading mt-4 leading-snug',
                    'pr-10' => $isOwner,
                ]) itemprop="name">{{ $announcement->title }}</h3>
            </div>

            {{-- Content --}}
            <div class="flex-1 px-6 py-5 space-y-4">
                <p class="text-sm text-subtle leading-relaxed line-clamp-3" itemprop="description">{{ Str::limit($announcement->description, 180) }}</p>

                <div class="flex flex-wrap gap-2">
                    @foreach ($shownInstruments as $instrument)
                        <x-pill variant="instrument">{{ $instrument->name }}</x-pill>
                    @endforeach
                    @if ($extraInstruments > 0)
                        <x-pill variant="instrument">+{{ $extraInstruments }}</x-pill>
                    @endif
                    @foreach ($shownGenres as $genre)
                        <x-pill variant="genre">{{ $genre->name }}</x-pill>
                    @endforeach
                    @if ($extraGenres > 0)
                        <x-pill variant="genre">+{{ $extraGenres }}</x-pill>
                    @endif
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 pt-2 pb-6 flex items-end justify-between gap-3">
                <div class="flex flex-col gap-1.5 text-xs text-caption min-w-0">
                    @if ($announcement->city)
                        <span class="inline-flex items-center gap-1 min-w-0"
                              itemprop="location" itemscope itemtype="https://schema.org/Place">
                            <x-icon name="map-pin" class="w-3 h-3 shrink-0"/>
                            <span class="truncate" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress"><span itemprop="addressLocality">{{ $announcement->city->name }}</span></span>
                        </span>
                    @endif
                    <span class="inline-flex items-center gap-1">
                        <x-icon name="clock" class="w-3 h-3 shrink-0"/>
                        {{ $announcement->created_at->format('d/m/Y') }}
                    </span>
                </div>
                <span class="inline-flex items-center gap-1.5 text-sm font-medium text-subtle group-hover:text-accent transition-colors duration-150 shrink-0">
                    {{ __('explore.card_see_announcement') }}
                    <x-icon name="arrow-right" class="w-3.5 h-3.5 motion-safe:transition-transform motion-safe:duration-150 group-hover:translate-x-0.5"/>
                </span>
            </div>

        </article>
    </a>
</div>
