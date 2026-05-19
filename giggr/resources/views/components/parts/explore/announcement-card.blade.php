@props(['announcement'])

@php $isOwner = auth()->check() && auth()->id() === $announcement->user_id; @endphp

<div class="relative h-full">
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
        class="block h-full focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent rounded-xl"
    >
        <article
            data-card="announcement"
            class="group flex flex-col w-full h-full rounded-xl overflow-hidden border border-dark/10 bg-white shadow-sm hover:shadow-md transition-shadow duration-200"
        >
            {{-- Header --}}
            <div class="px-5 pt-5 pb-4 border-b border-dark/[0.07]">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide {{ $announcement->type->color() }}">
                    {{ __($announcement->type->label()) }}
                </span>
                <h3 @class([
                    'font-heading text-xl text-dark mt-3 leading-snug',
                    'pr-10' => $isOwner,
                ])>{{ $announcement->title }}</h3>
            </div>

            {{-- Content --}}
            <div class="flex-1 px-5 py-4 space-y-3">
                <p class="text-sm text-dark/55 leading-relaxed line-clamp-3">{{ $announcement->description }}</p>

                <div class="flex flex-wrap gap-1.5">
                    @foreach ($announcement->instruments as $instrument)
                        <x-pill variant="instrument">{{ $instrument->name }}</x-pill>
                    @endforeach
                    @foreach ($announcement->genres as $genre)
                        <x-pill variant="genre">{{ $genre->name }}</x-pill>
                    @endforeach
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-5 pb-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 text-xs text-dark/45 min-w-0">
                    @if ($announcement->city)
                        <span class="inline-flex items-center gap-1 min-w-0">
                            <x-icon name="map-pin" class="w-3 h-3 shrink-0"/>
                            <span class="truncate">{{ $announcement->city->name }}</span>
                        </span>
                    @endif
                    <span class="inline-flex items-center gap-1 shrink-0">
                        <x-icon name="clock" class="w-3 h-3"/>
                        {{ $announcement->created_at->format('d/m/Y') }}
                    </span>
                </div>
                <span class="inline-flex items-center gap-1.5 text-sm font-medium text-dark/60 group-hover:text-accent transition-colors duration-150 shrink-0">
                    {{ __('explore.card_see_announcement') }}
                    <x-icon name="arrow-right" class="w-3.5 h-3.5 motion-safe:transition-transform motion-safe:duration-150 group-hover:translate-x-0.5"/>
                </span>
            </div>

        </article>
    </a>
</div>
