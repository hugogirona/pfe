@props(['profile'])

@php
    $name = $profile->user->full_name;
    $statusLabel = __($profile->status->label());
@endphp

<section aria-labelledby="identity-name-heading" class="text-center px-6 pb-5 border-b border-dark/[0.07]">
    <h2 id="identity-name-heading" class="font-heading text-2xl text-dark leading-tight">{{ $name }}</h2>

    @if ($profile->city)
        <p class="text-sm text-dark/50 mt-1 flex items-center justify-center gap-1">
            <x-icon name="map-pin" class="w-3.5 h-3.5"/>
            {{ $profile->city->name }}
        </p>
    @endif

    @if ($statusLabel)
        <span class="inline-flex mt-3 px-3 py-1 rounded-full bg-dark text-bg text-xs font-semibold tracking-wide">
            {{ $statusLabel }}
        </span>
    @endif
</section>
