@props([
    'name',
    'value',
    'label',
    'description' => null,
])

<label
    class="flex items-center gap-3 p-4 rounded-[6px] border cursor-pointer transition-colors duration-150
           border-dark/15 hover:border-dark/30
           has-[:checked]:border-accent has-[:checked]:bg-accent/5
           has-[:focus-visible]:ring-1 has-[:focus-visible]:ring-accent"
>
    <input
        type="radio"
        name="{{ $name }}"
        value="{{ $value }}"
        {{ $attributes }}
        class="peer sr-only"
    />

    <span class="relative w-5 h-5 shrink-0 rounded-full border-2 border-dark/25 peer-checked:border-accent transition-colors duration-150 flex items-center justify-center" aria-hidden="true">
        <span class="w-2.5 h-2.5 rounded-full bg-accent opacity-0 peer-checked:opacity-100 transition-opacity duration-150"></span>
    </span>

    <span class="min-w-0">
        <span class="block text-sm font-medium text-dark">{{ $label }}</span>
        @if ($description)
            <span class="block text-xs text-dark/50 mt-0.5 leading-relaxed">{{ $description }}</span>
        @endif
    </span>
</label>
