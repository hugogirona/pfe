@props([
    'label',
    'name',
    'required'    => false,
    'rows'        => 4,
    'placeholder' => '',
])

<div class="flex flex-col gap-1.5">
    <label for="{{ $name }}" class="text-sm font-medium text-dark/70">
        {{ $label }}@if($required)<span class="text-accent ml-0.5" aria-hidden="true">*</span>@endif
    </label>
    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if($required) required aria-required="true" @endif
        {{ $attributes->class([
            'w-full px-4 py-3 rounded-[6px] bg-white border border-dark/15 resize-none text-base',
            'text-dark placeholder:text-dark/30',
            'focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent',
            'transition-colors duration-150',
        ]) }}></textarea>
</div>
