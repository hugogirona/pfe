@props(['profile'])

@php $activeAds = $profile->user->announcements->count(); @endphp

<dl class="grid grid-cols-2 divide-x divide-dark/[0.07] border-b border-dark/[0.07]">
    <div class="flex flex-col-reverse items-center py-4 px-3">
        <dt class="text-[11px] text-dark/45 text-center leading-tight mt-0.5">{{ __('profile.stat_experience') }}</dt>
        <dd class="font-heading text-2xl text-dark">{{ $profile->experience_years }}</dd>
    </div>
    <div class="flex flex-col-reverse items-center py-4 px-3">
        <dt class="text-[11px] text-dark/45 text-center leading-tight mt-0.5">
            {{ trans_choice('profile.stat_ads', $activeAds) }}
        </dt>
        <dd class="font-heading text-2xl text-dark">{{ $activeAds }}</dd>
    </div>
</dl>
