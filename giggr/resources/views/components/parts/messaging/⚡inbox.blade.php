<?php

use App\Actions\SendMessage;
use App\Models\Conversation;
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
        return auth()->user()
            ->conversations()
            ->wherePivot('hidden_at', null)
            ->with(['participants.profile', 'latestMessage'])
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

        auth()->user()->conversations()->updateExistingPivot($id, [
            'last_read_at' => now(),
        ]);

        $this->currentConversationId = $id;
        $this->view = 'thread';
        $this->body = '';

        unset($this->currentConversation, $this->correspondent, $this->conversations, $this->visibleConversations);
    }

    public function backToList(): void
    {
        $this->currentConversationId = null;
        $this->draftRecipientId = null;
        $this->view = 'list';
        $this->body = '';

        unset($this->currentConversation, $this->correspondent);
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
    }
};
?>

@php $currentUserId = (int) auth()->id(); @endphp

{{-- Escape the modal's p-6 wrapper so tabs / header / compose can be edge-to-edge sticky --}}
<section
    class="-m-6 h-full flex flex-col bg-bg"
    aria-labelledby="messaging-inbox-heading"
>
    <h2 id="messaging-inbox-heading" class="sr-only">{{ __('messaging.title') }}</h2>

    @if ($view === 'list')

        {{-- Tabs (W3C tablist pattern: same panel, filtered by active tab) --}}
        <div
            role="tablist"
            aria-label="{{ __('messaging.aria_tabs') }}"
            class="flex shrink-0 border-b border-dark/10"
        >
            @foreach (['messages', 'requests'] as $tab)
                <button
                    type="button"
                    role="tab"
                    id="messaging-tab-{{ $tab }}"
                    aria-selected="{{ $activeTab === $tab ? 'true' : 'false' }}"
                    aria-controls="messaging-inbox-panel"
                    tabindex="{{ $activeTab === $tab ? '0' : '-1' }}"
                    wire:click="switchTab('{{ $tab }}')"
                    @class([
                        'flex-1 py-4 text-sm font-medium transition-colors duration-150 cursor-pointer relative focus-visible:outline-none focus-visible:bg-dark/5',
                        'text-dark' => $activeTab === $tab,
                        'text-dark/40 hover:text-dark/70' => $activeTab !== $tab,
                    ])
                >
                    {{ __('messaging.tab_'.$tab) }}
                    @if ($activeTab === $tab)
                        <span class="absolute inset-x-6 bottom-0 h-0.5 bg-accent rounded-full" aria-hidden="true"></span>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- Conversation list / empty state --}}
        <div
            id="messaging-inbox-panel"
            role="tabpanel"
            aria-labelledby="messaging-tab-{{ $activeTab }}"
            class="flex-1 overflow-y-auto"
        >
            @if ($this->visibleConversations->isEmpty())
                <div class="h-full flex flex-col items-center justify-center px-8 py-12 text-center" role="status">
                    <div class="w-16 h-16 rounded-full bg-dark/5 flex items-center justify-center mb-4" aria-hidden="true">
                        <x-icon name="chat-bubble" class="w-8 h-8 text-dark/30"/>
                    </div>
                    <p class="text-sm text-dark/50 italic">
                        {{ $activeTab === 'messages' ? __('messaging.empty_messages') : __('messaging.empty_requests') }}
                    </p>
                </div>
            @else
                <ul class="divide-y divide-dark/8">
                    @foreach ($this->visibleConversations as $conversation)
                        <li>
                            <x-parts.messaging.conversation-row
                                :conversation="$conversation"
                                :current-user-id="$currentUserId"
                            />
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

    @else

        {{-- Thread view --}}
        @php
            $convo = $this->currentConversation;
            $other = $this->correspondent;
            $otherName = $other?->full_name ?? '—';
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
            <x-parts.messaging.avatar :user="$other" class="w-9 h-9 text-sm"/>
            <h3 id="messaging-thread-heading" class="text-sm font-medium text-dark truncate">{{ $otherName }}</h3>
        </header>

        {{-- Messages --}}
        <div
            class="flex-1 overflow-y-auto px-5 py-4"
            x-data="{ scrollToBottom() { this.$nextTick(() => { this.$el.scrollTop = this.$el.scrollHeight; }); } }"
            x-init="scrollToBottom()"
            wire:key="thread-{{ $convo?->id ?? 0 }}"
            aria-labelledby="messaging-thread-heading"
            role="log"
            aria-live="polite"
        >
            @if ($convo && $convo->messages->isNotEmpty())
                <ol class="space-y-1.5">
                    @foreach ($convo->messages as $message)
                        @php
                            $isMine = (int) $message->sender_id === $currentUserId;
                            $authorLabel = $isMine ? __('messaging.you') : $otherName;
                        @endphp
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
        </div>

        {{-- Compose --}}
        <form
            wire:submit="send"
            aria-label="{{ __('messaging.compose_aria') }}"
            class="flex items-end gap-2 px-4 py-3 border-t border-dark/10 shrink-0 bg-bg"
        >
            <label for="messaging-compose-body" class="sr-only">
                {{ __('messaging.compose_label') }}
            </label>
            <div class="flex-1 min-h-10 rounded-2xl bg-dark/5 has-[textarea:focus]:bg-bg has-[textarea:focus]:ring-1 has-[textarea:focus]:ring-accent transition-colors duration-150">
                <textarea
                    id="messaging-compose-body"
                    name="body"
                    wire:model="body"
                    rows="1"
                    placeholder="{{ __('messaging.compose_placeholder') }}"
                    class="block w-full max-h-32 resize-none bg-transparent px-4 py-2 text-sm leading-6 text-dark placeholder-dark/40 focus:outline-none"
                    @keydown.enter.prevent="if (! $event.shiftKey) $wire.send()"
                ></textarea>
            </div>
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-accent text-bg hover:bg-accent/85 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed shrink-0"
                aria-label="{{ __('messaging.send') }}"
            >
                <x-icon name="arrow-right" class="w-4 h-4 -rotate-45"/>
            </button>
        </form>

    @endif

</section>
