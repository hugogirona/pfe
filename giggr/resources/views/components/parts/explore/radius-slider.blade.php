@props(['model', 'disabled' => false])

<section
    x-data="{
        radius: $wire.entangle($el.dataset.model),
        labelAny: $el.dataset.labelAny,
    }"
    data-model="{{ $model }}"
    data-label-any="{{ __('explore.filter_radius_any') }}"
    aria-labelledby="drawer-radius-heading"
>
    <div class="flex items-center justify-between mb-3">
        <h3 id="drawer-radius-heading" class="text-xs font-semibold uppercase tracking-widest text-caption">
            {{ __('explore.filter_radius') }}
        </h3>
        <span
            class="text-xs font-semibold text-accent tabular-nums"
            x-text="radius === 0 ? labelAny : radius + ' km'"
            aria-live="polite"
        >&nbsp;</span>
    </div>

    <input
        type="range"
        min="0"
        max="200"
        step="10"
        x-model.number="radius"
        @if ($disabled) disabled @endif
        aria-labelledby="drawer-radius-heading"
        aria-valuemin="0"
        aria-valuemax="200"
        :aria-valuenow="radius"
        class="range-slider"
    />

    @if ($disabled)
        <p class="text-xs text-caption italic mt-2">{{ __('explore.filter_radius_disabled_hint') }}</p>
    @endif
</section>
