@props([
    'label',
    'name',
    'required' => false,
])

<div class="flex flex-col gap-1.5">
    <label for="{{ $name }}" class="text-sm font-medium text-dark/70">
        {{ $label }}@if($required)<span class="text-accent ml-0.5" aria-hidden="true">*</span>@endif
    </label>
    <div class="relative">
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            @if($required) required aria-required="true" @endif
            {{ $attributes->class([
                'w-full appearance-none px-4 py-3 pr-10 rounded-[6px] bg-white border border-dark/15 text-base',
                'text-dark',
                'focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent',
                'transition-colors duration-150 cursor-pointer',
            ]) }}>
            {{ $slot }}
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center" aria-hidden="true">
            <x-icon name="chevron-down" class="w-4 h-4 text-dark/40" />
        </div>
    </div>
</div>
