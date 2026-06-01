@props([
    'label',
    'name',
    'id'           => null,
    'required'     => false,
    'autocomplete' => 'current-password',
    'placeholder'  => '',
])

@php $id ??= $name; @endphp

<div class="flex flex-col gap-1.5">
    <label for="{{ $id }}" class="text-sm font-medium text-dark/70">
        {{ $label }}@if($required)<span class="text-accent ml-0.5" aria-hidden="true">*</span>@endif
    </label>

    <div class="relative" x-data="{ show: false }">
        <input
            :type="show ? 'text' : 'password'"
            id="{{ $id }}"
            name="{{ $name }}"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($required) required @endif
            autocomplete="{{ $autocomplete }}"
            {{ $attributes->class([
                'w-full px-4 py-3 pr-12 rounded-[6px] bg-white border border-dark/15 text-base',
                'text-dark placeholder:text-dark/30',
                'focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent',
                'transition-colors duration-150',
            ]) }}
        />

        <button
            type="button"
            @click="show = !show"
            aria-label="{{ __('auth.password_show') }}"
            :aria-label="show ? @js(__('auth.password_hide')) : @js(__('auth.password_show'))"
            :aria-pressed="show"
            class="absolute inset-y-0 right-0 flex items-center px-3
                   text-dark/35 hover:text-dark transition-colors duration-150
                   cursor-pointer focus-visible:outline-none focus-visible:ring-1
                   focus-visible:ring-accent rounded-r-md"
        >
            <x-icon x-show="!show" name="eye" class="w-5 h-5" />
            <x-icon x-show="show" name="eye-slash" class="w-5 h-5" style="display:none" />
        </button>
    </div>
</div>
