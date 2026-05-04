@props([
    'profile',
    'isOwner'             => false,
    'allInstruments'      => [],
    'allGenres'           => [],
    'selectedInstruments' => [],
    'selectedGenres'      => [],
])

<div class="bg-white rounded-2xl border border-dark/10 shadow-sm overflow-hidden">

    <x-parts.profile.card-avatar :profile="$profile" :isOwner="$isOwner" />

    <x-parts.profile.card-identity :profile="$profile" />

    <x-parts.profile.card-stats :profile="$profile" />

    <x-parts.profile.card-instruments
        :profile="$profile"
        :isOwner="$isOwner"
        :allInstruments="$allInstruments"
        :selectedInstruments="$selectedInstruments"
    />

    <x-parts.profile.card-genres
        :profile="$profile"
        :isOwner="$isOwner"
        :allGenres="$allGenres"
        :selectedGenres="$selectedGenres"
    />

    {{-- Actions --}}
    @if (!$isOwner)
        <div class="px-6 pb-6 border-t border-dark/7 pt-5">
            <x-parts.profile.social-actions :profile="$profile" />
        </div>
    @endif

</div>
