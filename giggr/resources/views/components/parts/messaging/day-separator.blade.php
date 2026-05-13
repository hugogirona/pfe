@props(['date'])

@php
    /** @var \Illuminate\Support\Carbon $date */
    $today = today();
    $startOfDate = $date->copy()->startOfDay();
    $localized = $date->copy()->locale(app()->getLocale());

    $label = match (true) {
        $startOfDate->equalTo($today) => __('messaging.day_today'),
        $startOfDate->equalTo($today->copy()->subDay()) => __('messaging.day_yesterday'),
        $startOfDate->greaterThanOrEqualTo($today->copy()->subDays(6)) => ucfirst($localized->isoFormat('dddd')),
        $date->isSameYear($today) => ucfirst($localized->isoFormat('ddd D MMM')),
        default => ucfirst($localized->isoFormat('D MMM YYYY')),
    };
@endphp

<li class="flex justify-center py-3" role="separator">
    <time
        datetime="{{ $date->toIso8601String() }}"
        class="px-3 py-1 rounded-full bg-dark/8 text-dark/55 text-[11px] font-medium tracking-wide"
    >{{ $label }}</time>
</li>
