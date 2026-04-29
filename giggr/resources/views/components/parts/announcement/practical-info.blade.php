@props(['announcement'])

@php
$items = array_filter([
    !empty($announcement['city']) ? [
        'icon'  => 'map-pin',
        'label' => __('announcement.info_city'),
        'value' => $announcement['city'],
    ] : null,
    !empty($announcement['date']) ? [
        'icon'  => 'calendar',
        'label' => __('announcement.info_date'),
        'value' => $announcement['date'],
    ] : null,
    !empty($announcement['level']) ? [
        'icon'  => 'academic-cap',
        'label' => __('announcement.info_level'),
        'value' => $announcement['level'],
    ] : null,
    !empty($announcement['rehearsal_rhythm']) ? [
        'icon'  => 'repeat',
        'label' => __('announcement.info_rhythm'),
        'value' => $announcement['rehearsal_rhythm'],
    ] : null,
]);
@endphp

@if (!empty($items))
    <section aria-labelledby="info-heading" class="bg-white rounded-2xl border border-dark/10 shadow-sm p-6 md:p-8">

        <h2 id="info-heading" class="font-heading text-2xl text-dark mb-5">
            {{ __('announcement.info_title') }}
        </h2>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach ($items as $item)
                <div class="flex items-start gap-3 p-4 rounded-xl bg-bg border border-dark/[0.06]">
                    <div class="shrink-0 w-9 h-9 rounded-lg bg-white border border-dark/10 flex items-center justify-center text-accent shadow-sm">
                        <x-icon name="{{ $item['icon'] }}" class="w-4 h-4" />
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-widest text-dark/35 mb-0.5">
                            {{ $item['label'] }}
                        </dt>
                        <dd class="text-sm font-medium text-dark">{{ $item['value'] }}</dd>
                    </div>
                </div>
            @endforeach
        </dl>

    </section>
@endif
