@props([
    'label',
    'name',
    'required'    => false,
    'rows'        => 4,
    'placeholder' => '',
    'value'       => null,
])

<div class="flex flex-col gap-1.5">
    <label for="{{ $name }}" class="text-sm font-medium text-subtle">
        {{ $label }}@if($required)<span class="text-accent ml-0.5" aria-hidden="true">*</span>@endif
    </label>
    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror
        {{ $attributes->class([
            'w-full px-4 py-3 rounded-[6px] bg-white border border-dark/15 resize-none text-base',
            'text-body placeholder:text-placeholder',
            'focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent',
            'transition-colors duration-150',
        ])->class([
            'border-danger/60 focus:border-danger focus:ring-danger/30' => $errors->has($name),
        ]) }}>{{ old($name, $value) }}</textarea>
    @error($name)
        <p id="{{ $name }}-error" role="alert" class="text-xs text-danger mt-0.5">{{ $message }}</p>
    @enderror
</div>
