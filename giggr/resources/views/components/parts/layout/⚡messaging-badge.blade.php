<?php

use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public int $userId = 0;

    public int $count = 0;

    public function mount(): void
    {
        abort_unless(auth()->check(), 403);

        $this->userId = (int) auth()->id();
        $this->refreshCount();
    }

    #[On('echo-private:App.Models.User.{userId},.message.sent')]
    #[On('echo-private:App.Models.User.{userId},.messages.read')]
    #[On('messaging-updated')]
    public function refreshCount(): void
    {
        $this->count = auth()->user()->unreadConversationsCount();
    }
};
?>

<button
    type="button"
    @click="Livewire.dispatch('open-modal', { component: 'parts.messaging.inbox', title: @js(__('messaging.title')) })"
    class="relative w-10 h-10 text-subtle hover:text-accent transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-[6px] flex items-center justify-center"
    aria-label="{{ __('nav.aria_messaging') }}{{ $count > 0 ? ' ('.$count.')' : '' }}"
>
    <x-icon name="chat-bubble" class="w-7 h-7"/>
    @if ($count > 0)
        <span
            aria-hidden="true"
            class="absolute -top-0.5 -right-0.5 min-w-4.5 h-4.5 px-1 rounded-full bg-accent text-on-dark text-[0.625rem] font-bold flex items-center justify-center leading-none"
        >{{ $count > 99 ? '99+' : $count }}</span>
    @endif
</button>
