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
            <button
                type="button"
                x-show="!confirming"
                @click="confirming = true"
                class="h-11 px-4 rounded-md text-sm font-medium text-danger/70 hover:text-danger hover:bg-danger/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-danger"
            >
                {{ $deleteLabel }}
            </button>
            <div
                x-show="confirming"
                x-cloak
                x-transition:enter="transition-opacity duration-150 ease-out"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                class="flex items-center gap-2"
            >
                <button
                    type="button"
                    @click="confirming = false"
                    class="h-11 px-3 rounded-md text-sm font-medium text-dark/60 hover:text-dark hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
                >
                    {{ $cancelLabel }}
                </button>
                <button
                    type="button"
                    wire:click="{{ $deleteAction }}"
                    class="h-11 px-4 rounded-md text-sm font-medium bg-danger text-bg hover:opacity-90 transition-opacity duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-danger focus-visible:ring-offset-1"
                >
                    {{ $deleteConfirmLabel }}
                </button>
            </div>
        </div>
    @else
        <span></span>
    @endif

    <button
        type="submit"
        wire:loading.attr="disabled"
        wire:loading.class="opacity-60 cursor-not-allowed"
        class="h-11 px-6 rounded-md bg-dark text-bg text-sm font-medium hover:opacity-80 transition-opacity duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
    >
        <span wire:loading.remove wire:target="{{ $submitTarget }}">{{ $submitLabel }}</span>
        <span wire:loading wire:target="{{ $submitTarget }}">{{ $submittingLabel }}</span>
    </button>
</div>
