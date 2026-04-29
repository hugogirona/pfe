<?php

use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public ?string $current  = null;
    public string  $key      = '';
    public ?string $model_id = null;
    public string  $title    = '';

    #[On('open-modal')]
    public function open(string $component, string $title = '', ?string $model_id = null): void
    {
        $this->current  = $component;
        $this->title    = $title;
        $this->model_id = $model_id;
        $this->key      = uniqid();
    }

    #[On('close-modal')]
    public function close(): void
    {
        $this->current  = null;
        $this->model_id = null;
        $this->title    = '';
    }
};
?>

<div
    x-data="{ show: $wire.entangle('current').live }"
    x-show="show"
    class="fixed inset-0 z-50 overflow-hidden"
    style="display: none"
>
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="$wire.close()"
        class="fixed inset-0 bg-dark/40 backdrop-blur-sm"
        aria-hidden="true"
    ></div>

    <section
        x-show="show"
        x-transition:enter="transform transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        @keydown.escape.window="$wire.close()"
        class="fixed inset-y-0 right-0 z-50 flex flex-col w-full md:w-[520px] bg-bg shadow-2xl"
        role="dialog"
        aria-modal="true"
        :aria-label="$wire.title"
    >
        <div class="flex items-center justify-between px-6 py-5 border-b border-dark/10 shrink-0">
            <h2 class="font-heading text-xl text-dark">{{ $title }}</h2>
            <button
                wire:click="close"
                type="button"
                class="w-9 h-9 flex items-center justify-center rounded-full text-dark/40 hover:text-dark hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
                aria-label="{{ __('Fermer') }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5" aria-hidden="true">
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6">
            @if (!is_null($current))
                <livewire:dynamic-component
                    :is="$current"
                    :wire:key="$key"
                    :model_id="$model_id"
                />
            @endif
        </div>
    </section>
</div>
