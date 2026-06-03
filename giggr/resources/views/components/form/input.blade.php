@props([
    'label',
    'name',
    'type'         => 'text',
    'required'     => false,
    'autocomplete' => null,
    'placeholder'  => '',
    'value'        => null,
])

<div class="flex flex-col gap-1.5">
    <label for="{{ $name }}" class="text-sm font-medium text-subtle">
        {{ $label }}@if($required)<span class="text-accent ml-0.5" aria-hidden="true">*</span>@endif
    </label>
    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($required) required @endif
        @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror
        {{ $attributes->class([
            'w-full px-4 py-3 rounded-[6px] bg-white border border-dark/15 text-base',
            'text-body placeholder:text-placeholder',
            'focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent',
            'transition-colors duration-150',
        ])->class([
            'border-danger/60 focus:border-danger focus:ring-danger/30' => $errors->has($name),
        ]) }}
    />
    @error($name)
        <p id="{{ $name }}-error" role="alert" class="text-xs text-danger mt-0.5">{{ $message }}</p>
    @enderror
</div>
