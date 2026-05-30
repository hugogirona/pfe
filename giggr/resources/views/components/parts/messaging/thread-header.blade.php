@props([
    'user',
    'name',
    'conversation' => null,
    'currentUserId' => null,
    'showActions' => false,
])

@php
    $isPendingRequest = $conversation !== null
        && $conversation->accepted_at === null
        && (int) $conversation->requester_user_id !== (int) $currentUserId;
@endphp

<header class="flex items-center gap-3 px-5 py-3 border-b border-dark/10 shrink-0 bg-bg">
    <button
        type="button"
        wire:click="backToList"
        class="w-9 h-9 flex items-center justify-center rounded-full text-dark/60 hover:text-dark hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
        aria-label="{{ __('messaging.back') }}"
    >
        <x-icon name="arrow-right" class="w-5 h-5 rotate-180"/>
    </button>
    <x-parts.messaging.avatar :user="$user" class="w-9 h-9 text-sm"/>
    <h3 id="messaging-thread-heading" class="text-sm font-medium text-dark truncate min-w-0 flex-1">
        @if ($user)
            <a
                href="{{ route('profile', ['id' => $user->id]) }}"
                wire:navigate
                class="hover:underline focus-visible:underline focus-visible:outline-none cursor-pointer"
                aria-label="{{ __('messaging.open_profile', ['name' => $name]) }}"
            >{{ $name }}</a>
        @else
            {{ $name }}
        @endif
    </h3>

    @if ($isPendingRequest)
        <div class="flex items-center gap-1.5 shrink-0">
            <button
                type="button"
                wire:click="declineRequest({{ $conversation->id }})"
                wire:confirm="{{ __('messaging.decline_confirm') }}"
                class="px-3 py-1.5 rounded-md text-xs font-medium text-dark/60 hover:text-dark hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
            >
                {{ __('messaging.decline_request') }}
            </button>
            <button
                type="button"
                wire:click="acceptRequest({{ $conversation->id }})"
                class="px-3 py-1.5 rounded-md text-xs font-medium bg-accent text-bg hover:opacity-90 transition-opacity duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
            >
                {{ __('messaging.accept_request') }}
            </button>
        </div>
    @elseif ($showActions && $conversation)
        <div class="flex items-center gap-0.5 shrink-0">
            <button
                type="button"
                @click="$dispatch('thread-action-confirm', { action: 'block' })"
                class="w-9 h-9 flex items-center justify-center rounded-full text-dark/60 hover:text-danger hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
                aria-label="{{ __('messaging.block_correspondent_aria', ['name' => $name]) }}"
            >
                <x-icon name="no-symbol" class="w-5 h-5"/>
            </button>
            <button
                type="button"
                @click="$dispatch('thread-action-confirm', { action: 'delete' })"
                class="w-9 h-9 flex items-center justify-center rounded-full text-dark/60 hover:text-danger hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
                aria-label="{{ __('messaging.delete_conversation_aria', ['name' => $name]) }}"
            >
                <x-icon name="trash" class="w-5 h-5"/>
            </button>
        </div>
    @endif
</header>
