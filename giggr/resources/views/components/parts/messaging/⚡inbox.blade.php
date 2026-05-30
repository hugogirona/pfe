<?php

use App\Actions\HideConversation;
use App\Actions\MarkConversationAsRead;
use App\Actions\SendMessage;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public string $view = 'list';

    public ?int $currentConversationId = null;

    public ?int $draftRecipientId = null;

    public string $activeTab = 'messages';

    public string $body = '';

    public ?int $newMessageMarkerId = null;

    public int $newMessageMarkerCount = 0;

    /**
     * @return array<string, string>
     */
    public function getListeners(): array
    {
        $userId = (int) auth()->id();

        return [
            "echo-private:App.Models.User.{$userId},.message.sent" => 'incomingMessage',
        ];
    }

    public function incomingMessage(array $payload = []): void
    {
        unset($this->conversations, $this->visibleConversations);

        $isViewingMatchingThread = $this->view === 'thread'
            && isset($payload['conversation_id'])
            && (int) $payload['conversation_id'] === $this->currentConversationId;

        if ($isViewingMatchingThread) {
            $this->messageReceived($payload);
        }
    }

    public function messageReceived(array $payload = []): void
    {
        if ($this->view !== 'thread' || $this->currentConversationId === null) {
            return;
        }

        if (isset($payload['conversation_id']) && (int) $payload['conversation_id'] !== $this->currentConversationId) {
            return;
        }

        $this->markConversationAsRead($this->currentConversationId);

        unset($this->currentConversation);

        $this->dispatch('thread-updated');
    }


    public function readReceiptReceived(array $payload = []): void
    {
        if ($this->view !== 'thread' || $this->currentConversationId === null) {
            return;
        }

        if (isset($payload['conversation_id']) && (int) $payload['conversation_id'] !== $this->currentConversationId) {
            return;
        }

        unset($this->currentConversation);
    }

    public function mount(?string $model_id = null): void
    {
        abort_unless(auth()->check(), 403);

        if ($model_id === null) {
            return;
        }

        $targetId = (int) $model_id;
        if ($targetId === (int) auth()->id()) {
            return;
        }

        $target = User::find($targetId);
        if ($target === null) {
            return;
        }

        $currentId = (int) auth()->id();
        [$low, $high] = $currentId < $targetId
            ? [$currentId, $targetId]
            : [$targetId, $currentId];

        $existing = Conversation::query()
            ->where('user_a_id', $low)
            ->where('user_b_id', $high)
            ->first();

        if ($existing !== null) {
            $this->openConversation($existing->id);

            return;
        }

        $this->draftRecipientId = $targetId;
        $this->view = 'thread';
    }

    #[Computed]
    public function conversations(): Collection
    {
        $userId = (int) auth()->id();

        return auth()->user()
            ->conversations()
            ->wherePivot('hidden_at', null)
            ->with(['participants.profile', 'latestMessage'])
            ->withCount(['messages as unread_count_for_me' => function ($q) use ($userId): void {
                $q->where('sender_id', '!=', $userId)->whereNull('read_at');
            }])
            ->orderByDesc('last_message_at')
            ->orderByDesc('conversations.created_at')
            ->get();
    }

    #[Computed]
    public function visibleConversations(): Collection
    {
        $userId = (int) auth()->id();

        return $this->conversations->filter(function (Conversation $c) use ($userId): bool {
            return $this->activeTab === 'messages'
                ? ($c->accepted_at !== null || (int) $c->requester_user_id === $userId)
                : ($c->accepted_at === null && (int) $c->requester_user_id !== $userId);
        })->values();
    }

    #[Computed]
    public function messagesCount(): int
    {
        $userId = (int) auth()->id();

        return $this->conversations->filter(
            fn (Conversation $c): bool => ($c->accepted_at !== null || (int) $c->requester_user_id === $userId)
                && (int) $c->unread_count_for_me > 0,
        )->count();
    }

    #[Computed]
    public function requestsCount(): int
    {
        $userId = (int) auth()->id();

        return $this->conversations->filter(
            fn (Conversation $c): bool => $c->accepted_at === null
                && (int) $c->requester_user_id !== $userId
                && (int) $c->unread_count_for_me > 0,
        )->count();
    }

    #[Computed]
    public function currentConversation(): ?Conversation
    {
        if ($this->currentConversationId === null) {
            return null;
        }

        return Conversation::query()
            ->with(['messages.sender', 'participants.profile'])
            ->find($this->currentConversationId);
    }

    #[Computed]
    public function correspondent(): ?User
    {
        if ($this->currentConversation) {
            return $this->currentConversation->participants
                ->firstWhere('id', '!=', auth()->id());
        }

        return $this->draftRecipientId !== null
            ? User::find($this->draftRecipientId)
            : null;
    }

    /**
     * Returns null when messaging is allowed, 'blocked-by-me' if the current
     * user has blocked the correspondent, or 'blocked-by-them' if the
     * correspondent has blocked the current user.
     */
    #[Computed]
    public function blockState(): ?string
    {
        $correspondent = $this->correspondent;
        if ($correspondent === null) {
            return null;
        }

        $me = auth()->user();
        if ($me->hasBlocked($correspondent)) {
            return 'blocked-by-me';
        }
        if ($correspondent->hasBlocked($me)) {
            return 'blocked-by-them';
        }

        return null;
    }

    public function switchTab(string $tab): void
    {
        if (in_array($tab, ['messages', 'requests'], true)) {
            $this->activeTab = $tab;
        }
    }

    public function openConversation(int $id): void
    {
        abort_unless(
            auth()->user()->conversations()->whereKey($id)->exists(),
            403,
        );

        $userId = (int) auth()->id();

        $firstUnread = Message::query()
            ->where('conversation_id', $id)
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->orderBy('created_at')
            ->orderBy('id')
            ->first(['id']);

        if ($firstUnread !== null) {
            $this->newMessageMarkerId = (int) $firstUnread->id;
            $this->newMessageMarkerCount = Message::query()
                ->where('conversation_id', $id)
                ->where('sender_id', '!=', $userId)
                ->whereNull('read_at')
                ->count();
        } else {
            $this->dismissNewMessageMarker();
        }

        $this->markConversationAsRead($id);

        $this->currentConversationId = $id;
        $this->view = 'thread';
        $this->body = '';

        unset($this->currentConversation, $this->correspondent, $this->conversations, $this->visibleConversations);
    }

    public function dismissNewMessageMarker(): void
    {
        $this->newMessageMarkerId = null;
        $this->newMessageMarkerCount = 0;
    }

    private function markConversationAsRead(int $conversationId): void
    {
        app(MarkConversationAsRead::class)->execute(auth()->user(), $conversationId);
        $this->dispatch('messaging-updated');
    }

    public function backToList(): void
    {
        $this->currentConversationId = null;
        $this->draftRecipientId = null;
        $this->view = 'list';
        $this->body = '';
        $this->dismissNewMessageMarker();

        unset($this->currentConversation, $this->correspondent);
    }

    public function deleteConversation(int $id): void
    {
        abort_unless(
            auth()->user()->conversations()->whereKey($id)->exists(),
            403,
        );

        app(HideConversation::class)->execute(auth()->user(), $id);

        if ($this->currentConversationId === $id) {
            $this->backToList();
        }

        unset($this->conversations, $this->visibleConversations);
        $this->dispatch('messaging-updated');
    }

    public function blockCorrespondent(): void
    {
        abort_unless($this->view === 'thread', 403);

        $correspondent = $this->correspondent;
        abort_unless($correspondent !== null, 404);

        auth()->user()->block($correspondent);
        $this->backToList();

        unset($this->conversations, $this->visibleConversations);
        $this->dispatch('messaging-updated');
    }

    public function acceptRequest(int $id): void
    {
        $conversation = $this->resolvePendingRequest($id);

        if ($conversation->accepted_at === null) {
            $conversation->update(['accepted_at' => now()]);
        }

        unset($this->currentConversation, $this->conversations, $this->visibleConversations);
    }

    public function declineRequest(int $id): void
    {
        $conversation = $this->resolvePendingRequest($id);

        auth()->user()->conversations()->updateExistingPivot($conversation->id, [
            'hidden_at' => now(),
        ]);

        if ($this->currentConversationId === $conversation->id) {
            $this->backToList();
        }

        unset($this->conversations, $this->visibleConversations);
    }

    private function resolvePendingRequest(int $id): Conversation
    {
        $userId = (int) auth()->id();

        $conversation = auth()->user()
            ->conversations()
            ->whereKey($id)
            ->first();

        abort_unless($conversation !== null, 403);
        abort_unless($conversation->requester_user_id !== $userId, 403);

        return $conversation;
    }

    public function send(): void
    {
        abort_unless(
            $this->view === 'thread' && ($this->currentConversationId !== null || $this->draftRecipientId !== null),
            403,
        );

        $correspondent = $this->correspondent;
        abort_unless($correspondent !== null, 404);

        $message = app(SendMessage::class)->execute(auth()->user(), $correspondent, $this->body);

        if ($this->draftRecipientId !== null) {
            $this->currentConversationId = $message->conversation_id;
            $this->draftRecipientId = null;
        }

        $this->body = '';

        unset($this->currentConversation, $this->conversations, $this->visibleConversations);

        $this->dispatch('thread-updated');
    }
};
?>

@php $currentUserId = (int) auth()->id(); @endphp

<section
    class="-m-6 h-full flex flex-col bg-bg"
    aria-labelledby="messaging-inbox-heading"
>
    <h2 id="messaging-inbox-heading" class="sr-only">{{ __('messaging.title') }}</h2>

    @if ($view === 'list')

        <x-parts.messaging.inbox-tabs
            :active-tab="$activeTab"
            :messages-count="$this->messagesCount"
            :requests-count="$this->requestsCount"
        />

        <x-parts.messaging.conversation-list
            :conversations="$this->visibleConversations"
            :active-tab="$activeTab"
            :current-user-id="$currentUserId"
        />

    @else

        @php
            $convo = $this->currentConversation;
            $other = $this->correspondent;
            $otherName = $other?->full_name ?? '—';
        @endphp

        @php $showThreadActions = $convo !== null && $convo->accepted_at !== null; @endphp

        <div class="contents">
            <x-parts.messaging.thread-header
                :user="$other"
                :name="$otherName"
                :conversation="$convo"
                :current-user-id="$currentUserId"
                :show-actions="$showThreadActions"
            />

            @if ($showThreadActions)
                <x-parts.messaging.thread-confirm-bar
                    :conversation-id="$convo->id"
                    :other-name="$otherName"
                />
            @endif

            <x-parts.messaging.thread-messages
                :conversation="$convo"
                :other-name="$otherName"
                :current-user-id="$currentUserId"
                :new-message-marker-id="$newMessageMarkerId"
                :new-message-marker-count="$newMessageMarkerCount"
            />

            <x-parts.messaging.compose-form
                :conversation-id="$convo?->id"
                :block-state="$this->blockState"
            />
        </div>

    @endif

</section>
