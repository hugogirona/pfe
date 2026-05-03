@props(['profile'])

@php $activeAds = $profile->user->announcements->count(); @endphp

<dl class="grid grid-cols-2 divide-x divide-dark/[0.07] border-b border-dark/[0.07]">
    <div class="flex flex-col items-center py-4 px-3">
        <dd class="font-heading text-2xl text-dark">{{ $profile->experience_years }}</dd>
        <dt class="text-[11px] text-dark/45 text-center leading-tight mt-0.5">{{ __('profile.stat_experience') }}</dt>
    </div>
    <div class="flex flex-col items-center py-4 px-3">
        <dd class="font-heading text-2xl text-dark">{{ $activeAds }}</dd>
        <dt class="text-[11px] text-dark/45 text-center leading-tight mt-0.5">
            {{ trans_choice('profile.stat_ads', $activeAds) }}
        </dt>
    </div>
</dl>
