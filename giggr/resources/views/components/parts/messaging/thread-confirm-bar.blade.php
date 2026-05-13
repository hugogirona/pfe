@props([
    'conversationId',
    'otherName',
])

<div
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
            <p class="text-sm text-dark/80 flex-1 min-w-0">
                <span x-show="confirm === 'delete'">{{ __('messaging.delete_confirm') }}</span>
                <span x-show="confirm === 'block'">{{ __('profile.block_confirm', ['name' => $otherName]) }}</span>
            </p>
            <button
                type="button"
                @click="confirm = null"
                class="px-3 py-1.5 rounded-md text-xs font-medium text-dark/60 hover:text-dark hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
            >
                {{ __('profile.cancel') }}
            </button>
            <button
                type="button"
                x-show="confirm === 'delete'"
                wire:click="deleteConversation({{ $conversationId }})"
                @click="confirm = null"
                class="px-3 py-1.5 rounded-md text-xs font-medium bg-danger text-bg hover:opacity-90 transition-opacity duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-danger focus-visible:ring-offset-1"
            >
                {{ __('messaging.delete') }}
            </button>
            <button
                type="button"
                x-show="confirm === 'block'"
                wire:click="blockCorrespondent"
                @click="confirm = null"
                class="px-3 py-1.5 rounded-md text-xs font-medium bg-danger text-bg hover:opacity-90 transition-opacity duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-danger focus-visible:ring-offset-1"
            >
                {{ __('profile.block') }}
            </button>
        </div>
    </div>
</div>
