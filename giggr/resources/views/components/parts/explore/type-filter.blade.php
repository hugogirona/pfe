@props([
    'types',
    'selected',
    'toggleMethod' => 'toggleType',
])

<section>
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-xs font-semibold uppercase tracking-widest text-caption">
            {{ __('explore.filter_types') }}
        </h3>
        @if (count($selected) > 0)
            <span class="text-xs text-accent font-semibold">{{ count($selected) }}</span>
        @endif
    </div>

    <div class="flex flex-wrap gap-2" role="group" aria-label="{{ __('explore.filter_types') }}">
        @foreach ($types as $type)
            <button
                type="button"
                wire:click="{{ $toggleMethod }}('{{ $type['value'] }}')"
                @class([
                    'h-9 px-4 rounded-full border text-sm font-medium transition-all duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent',
                    'bg-accent text-on-dark border-accent' => in_array($type['value'], $selected),
                    'bg-white text-subtle border-dark/15 hover:border-dark/40 hover:text-body' => !in_array($type['value'], $selected),
                ])
                aria-pressed="{{ in_array($type['value'], $selected) ? 'true' : 'false' }}"
            >{{ $type['label'] }}</button>
        @endforeach
    </div>
</section>
