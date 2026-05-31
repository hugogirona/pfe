@props([
    'conversation',
    'otherName',
    'currentUserId',
    'newMessageMarkerId',
    'newMessageMarkerCount',
])

<div
    class="flex-1 overflow-y-auto px-5 py-4"
    x-data="{
        channel: null,
        readHandler: null,
        typingHandler: null,
        closedHandler: null,
        isTyping: false,
        typingTimer: null,
        scrollToBottom() { this.$nextTick(() => { this.$el.scrollTop = this.$el.scrollHeight; }); },
        showTyping() {
            this.isTyping = true;
            this.scrollToBottom();
            clearTimeout(this.typingTimer);
            this.typingTimer = setTimeout(() => this.isTyping = false, 3000);
        },
        subscribeChannel() {
            const id = @js($conversation?->id);
            if (! id || typeof window.Echo === 'undefined') return;
            this.channel = window.Echo.private('conversation.' + id);
            this.readHandler = (e) => $wire.readReceiptReceived(e);
            this.typingHandler = () => this.showTyping();
            this.closedHandler = () => $wire.$refresh();
            this.channel.listen('.messages.read', this.readHandler);
            this.channel.listen('.conversation.closed', this.closedHandler);
            this.channel.listenForWhisper('typing', this.typingHandler);
        },
        destroy() {
            if (this.channel) {
                if (this.readHandler) this.channel.stopListening('.messages.read', this.readHandler);
                if (this.closedHandler) this.channel.stopListening('.conversation.closed', this.closedHandler);
                if (this.typingHandler) this.channel.stopListeningForWhisper('typing', this.typingHandler);
            }
            clearTimeout(this.typingTimer);
        },
    }"
    x-init="scrollToBottom(); subscribeChannel();"
    @thread-updated.window="isTyping = false; scrollToBottom();"
    wire:key="thread-{{ $conversation?->id ?? 0 }}"
    aria-labelledby="messaging-thread-heading"
    role="log"
    aria-live="polite"
>
    @if ($conversation && $conversation->messages->isNotEmpty())
        <ol class="space-y-1.5">
            @php $prevDate = null; @endphp
            @foreach ($conversation->messages as $message)
                @php
                    $messageDate = $message->created_at->copy()->startOfDay();
                    $showDaySeparator = $prevDate === null || ! $messageDate->equalTo($prevDate);
                    $prevDate = $messageDate;
                    $isMine = (int) $message->sender_id === $currentUserId;
                    $authorLabel = $isMine ? __('messaging.you') : $otherName;
                    $showNewMessageMarker = $newMessageMarkerId !== null
                        && (int) $message->id === $newMessageMarkerId
                        && $newMessageMarkerCount > 0;
                @endphp
                @if ($showDaySeparator)
                    <x-parts.messaging.day-separator :date="$message->created_at"/>
                @endif
                @if ($showNewMessageMarker)
                    <li class="flex items-center gap-2 px-1 py-2" role="separator">
                        <span class="h-px flex-1 bg-accent/30" aria-hidden="true"></span>
                        <span class="text-[10px] font-semibold uppercase tracking-wider text-accent">
                            {{ trans_choice('messaging.new_messages', $newMessageMarkerCount, ['count' => $newMessageMarkerCount]) }}
                        </span>
                        <span class="h-px flex-1 bg-accent/30" aria-hidden="true"></span>
                    </li>
                @endif
                <li class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                    <x-parts.messaging.message-bubble
                        :message="$message"
                        :is-mine="$isMine"
                        :author-label="$authorLabel"
                    />
                </li>
            @endforeach
        </ol>
    @else
        <p class="h-full flex items-center justify-center text-sm text-dark/40 italic">
            {{ __('messaging.no_messages_yet') }}
        </p>
    @endif

    <x-parts.messaging.typing-indicator :name="$otherName"/>
</div>
