@props([
    'profile',
    'followedProfileIds' => null,
])

@php
    $name              = $profile->user->full_name;
    $url               = route('profile', ['id' => $profile->id]);
    $isFollowing       = is_array($followedProfileIds) ? in_array($profile->id, $followedProfileIds, true) : null;
    $pillLimit         = 2;
    $shownInstruments  = $profile->instruments->take($pillLimit)->pluck('name');
    $extraInstruments  = max(0, $profile->instruments->count() - $pillLimit);
    $shownGenres       = $profile->genres->take($pillLimit)->pluck('name');
    $extraGenres       = max(0, $profile->genres->count() - $pillLimit);
@endphp

<div {{ $attributes->class('relative') }} itemscope itemtype="https://schema.org/Person">
    <link itemprop="url" href="{{ $url }}">
    <a
        href="{{ $url }}"
        @guest x-data @click.prevent="$dispatch('open-auth-modal')" @endguest
        @auth wire:navigate @endauth
        class="block focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-xl"
    >
        <article
            class="group flex flex-col w-full rounded-xl overflow-hidden border border-dark/10 bg-white shadow-sm hover:shadow-md transition-shadow duration-200"
        >
            <div class="relative h-52 shrink-0 bg-dark/5">
                @if ($profile->medium)
                    <img
                        src="{{ $profile->medium }}"
                        alt="Photo de {{ $name }}"
                        class="absolute inset-0 w-full h-full object-cover object-center"
                        loading="lazy"
                        itemprop="image"
                    />
                @else
                    <div class="absolute inset-0 flex items-center justify-center bg-pastel-taupe">
                        <span class="font-heading text-4xl text-subtle select-none">
                            {{ mb_substr($name, 0, 1) }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Content --}}
            <div class="px-5 py-4 space-y-3">
                <div>
                    <h3 class="font-heading text-xl text-heading" itemprop="name">{{ $name }}</h3>
                    <p class="text-sm text-caption mt-0.5 min-h-lh">
                        @if($profile->age)
                            {{ __('explore.card_years', ['n' => $profile->age]) }}
                        @endif
                        @if($profile->age && $profile->city)
                            {{' . '}}
                        @endif
                        @if($profile->city)
                            <span itemprop="address" itemscope itemtype="https://schema.org/PostalAddress"><span itemprop="addressLocality">{{ $profile->city->name }}</span></span>
                        @endif
                    </p>
                </div>

                <div class="flex flex-wrap content-start gap-1.5 min-h-13.5">
                    @foreach ($shownInstruments as $instrument)
                        <x-pill variant="instrument">{{ $instrument }}</x-pill>
                    @endforeach
                    @if ($extraInstruments > 0)
                        <x-pill variant="instrument">+{{ $extraInstruments }}</x-pill>
                    @endif
                    @foreach ($shownGenres as $genre)
                        <x-pill variant="genre">{{ $genre }}</x-pill>
                    @endforeach
                    @if ($extraGenres > 0)
                        <x-pill variant="genre">+{{ $extraGenres }}</x-pill>
                    @endif
                </div>

                <div class="text-sm leading-relaxed min-h-[2lh]">
                    @if (filled($profile->bio))
                        <p class="text-subtle line-clamp-2">{{ Str::limit($profile->bio, 120) }}</p>
                    @endif
                </div>
            </div>

            {{-- CTA --}}
            <div
                class="relative overflow-hidden flex items-center min-h-12 px-5 border-t border-dark/10 text-sm font-medium">
                <span
                    class="absolute inset-0 translate-x-[-101%] bg-accent motion-safe:transition-transform motion-safe:duration-300 motion-safe:ease-out group-hover:translate-x-0"
                    aria-hidden="true"></span>
                <span
                    class="relative flex items-center gap-2 text-body group-hover:text-on-dark motion-safe:transition-colors motion-safe:duration-100">
                    {{ __('explore.card_see_profile') }}
                    <x-icon name="arrow-right" class="w-4 h-4" />
                </span>
            </div>
        </article>
    </a>

    <div class="absolute top-3 right-3 z-10">
        <livewire:parts.social.follow-button
            :profile-id="$profile->id"
            :musician-name="$name"
            :owner-id="$profile->user_id"
            :is-following="$isFollowing"
            :wire:key="'follow-profile-'.$profile->id"
        />
    </div>
</div>
