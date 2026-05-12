@props(['conversation', 'currentUserId'])

@php
    /** @var \App\Models\User|null $other */
    $other = $conversation->participants->firstWhere('id', '!=', $currentUserId);
    $when = $conversation->last_message_at?->diffForHumans(short: true);
@endphp

<button
    type="button"
    wire:click="openConversation({{ $conversation->id }})"
    aria-label="{{ __('messaging.open_conversation_with', ['name' => $other?->full_name ?? '—']) }}"
    class="w-full flex items-center gap-3 px-6 py-3.5 text-left hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:bg-dark/5"
>
    <x-parts.messaging.avatar :user="$other" class="w-12 h-12 text-base"/>

    <div class="flex-1 min-w-0">
        <div class="flex items-baseline gap-2">
            <span class="text-sm font-medium text-dark truncate">
                {{ $other?->full_name ?? '—' }}
            </span>
            @if ($when)
                <time
                    datetime="{{ $conversation->last_message_at?->toIso8601String() }}"
                    class="ml-auto text-[11px] text-dark/40 shrink-0"
                >{{ $when }}</time>
            @endif
        </div>
        <p class="text-xs text-dark/50 truncate mt-0.5">
            {{ $conversation->latestMessage?->body ?? __('messaging.no_messages_yet') }}
        </p>
    </div>
</button>
