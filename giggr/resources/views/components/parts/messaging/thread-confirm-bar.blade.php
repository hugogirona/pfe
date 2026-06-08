@props([
    'conversationId',
    'otherName',
])

<div
    x-data="{ confirm: null }"
    @thread-action-confirm.window="confirm = $event.detail.action"
    class="grid shrink-0 transition-[grid-template-rows] duration-300 ease-[cubic-bezier(0.32,0.72,0,1)]"
    :class="confirm ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'"
    :aria-hidden="confirm ? 'false' : 'true'"
>
    <div class="overflow-hidden">
        <div
            role="alertdialog"
            aria-live="polite"
            class="px-5 py-3 border-b border-dark/10 bg-danger/5 flex items-center gap-3 transition-opacity duration-200 ease-out"
            :class="confirm ? 'opacity-100' : 'opacity-0 pointer-events-none'"
        >
            <p class="text-sm text-subtle flex-1 min-w-0">
                <span x-show="confirm === 'delete'">{{ __('messaging.delete_confirm') }}</span>
                <span x-show="confirm === 'block'">{{ __('profile.block_confirm', ['name' => $otherName]) }}</span>
            </p>
            <x-cta
                variant="simple"
                size="sm"
                type="button"
                @click="confirm = null"
            >
                {{ __('profile.cancel') }}
            </x-cta>
            <x-cta
                variant="danger-solid"
                size="sm"
                type="button"
                x-show="confirm === 'delete'"
                wire:click="deleteConversation({{ $conversationId }})"
                @click="confirm = null"
            >
                {{ __('messaging.delete') }}
            </x-cta>
            <x-cta
                variant="danger-solid"
                size="sm"
                type="button"
                x-show="confirm === 'block'"
                wire:click="blockCorrespondent"
                @click="confirm = null"
            >
                {{ __('profile.block') }}
            </x-cta>
        </div>
    </div>
</div>
