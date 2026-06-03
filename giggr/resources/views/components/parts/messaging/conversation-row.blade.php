@props(['conversation', 'currentUserId'])

@php
    /** @var \App\Models\User|null $other */
    $other = $conversation->participants->firstWhere('id', '!=', $currentUserId);
    $when = $conversation->last_message_at?->diffForHumans(short: true);
    $unread = (int) ($conversation->unread_count_for_me ?? 0);
    $hasUnread = $unread > 0;
@endphp

<button
    type="button"
    wire:click="openConversation({{ $conversation->id }})"
    aria-label="{{ __('messaging.open_conversation_with', ['name' => $other?->full_name ?? '—']) }}{{ $hasUnread ? ' — '.trans_choice('messaging.unread_count', $unread, ['count' => $unread]) : '' }}"
    class="w-full flex items-center gap-3 px-6 py-3.5 text-left hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:bg-dark/5"
>
    <x-parts.messaging.avatar :user="$other" class="w-12 h-12 text-base"/>

    <div class="flex-1 min-w-0">
        <div class="flex items-baseline gap-2">
            <span @class([
                'text-sm truncate',
                'font-semibold text-body' => $hasUnread,
                'font-medium text-body' => ! $hasUnread,
            ])>
                {{ $other?->full_name ?? '—' }}
            </span>
            @if ($when)
                <time
                    datetime="{{ $conversation->last_message_at?->toIso8601String() }}"
                    @class([
                        'ml-auto text-[11px] shrink-0',
                        'text-accent font-semibold' => $hasUnread,
                        'text-caption' => ! $hasUnread,
                    ])
                >{{ $when }}</time>
            @endif
        </div>
        <div class="flex items-center gap-2 mt-0.5">
            <p @class([
                'text-xs truncate flex-1',
                'text-subtle font-medium' => $hasUnread,
                'text-subtle' => ! $hasUnread,
            ])>
                {{ $conversation->latestMessage?->body ?? __('messaging.no_messages_yet') }}
            </p>
            @if ($hasUnread)
                <span
                    aria-hidden="true"
                    class="shrink-0 min-w-[18px] h-[18px] px-1 rounded-full bg-accent text-on-dark text-[10px] font-bold flex items-center justify-center leading-none"
                >{{ $unread > 99 ? '99+' : $unread }}</span>
            @endif
        </div>
    </div>
</button>
