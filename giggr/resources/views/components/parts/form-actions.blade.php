@props([
    'submitLabel',
    'submittingLabel' => null,
    'submitTarget' => 'save',
    'showDelete' => false,
    'deleteLabel' => null,
    'deleteConfirmLabel' => null,
    'cancelLabel' => null,
    'deleteAction' => 'delete',
])

@php
    $submittingLabel ??= $submitLabel;
    $deleteConfirmLabel ??= __('common.confirm_delete');
    $cancelLabel ??= __('common.cancel');
@endphp

<div class="flex items-center justify-between gap-3 pt-2 border-t border-dark/10">
    @if ($showDelete)
        <div x-data="{ confirming: false }">
            <x-cta
                variant="danger"
                size="form"
                class="px-4"
                type="button"
                x-show="!confirming"
                @click="confirming = true"
            >
                {{ $deleteLabel }}
            </x-cta>
            <div
                x-show="confirming"
                x-cloak
                x-transition:enter="transition-opacity duration-150 ease-out"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                class="flex items-center gap-2"
            >
                <x-cta
                    variant="simple"
                    size="form"
                    class="px-3"
                    type="button"
                    @click="confirming = false"
                >
                    {{ $cancelLabel }}
                </x-cta>
                <x-cta
                    variant="danger-solid"
                    size="form"
                    class="px-4"
                    type="button"
                    wire:click="{{ $deleteAction }}"
                >
                    {{ $deleteConfirmLabel }}
                </x-cta>
            </div>
        </div>
    @else
        <span></span>
    @endif

    <x-cta
        variant="dark"
        size="form"
        class="px-6"
        type="submit"
        wire:loading.attr="disabled"
        wire:loading.class="opacity-60 cursor-not-allowed"
    >
        <span wire:loading.remove wire:target="{{ $submitTarget }}">{{ $submitLabel }}</span>
        <span wire:loading wire:target="{{ $submitTarget }}">{{ $submittingLabel }}</span>
    </x-cta>
</div>
