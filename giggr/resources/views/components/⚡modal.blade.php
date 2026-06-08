<?php

use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public ?string $current  = null;
    public string  $key      = '';
    public ?string $model_id = null;
    public ?string $media_id = null;
    public string  $title    = '';

    #[On('open-modal')]
    public function open(string $component, string $title = '', ?string $model_id = null, ?string $media_id = null): void
    {
        $this->current  = $component;
        $this->title    = $title;
        $this->model_id = $model_id;
        $this->media_id = $media_id;
        $this->key      = uniqid();
    }

    #[On('close-modal')]
    public function close(): void
    {
        $this->current  = null;
        $this->model_id = null;
        $this->media_id = null;
        $this->title    = '';
    }
};
?>

<div
    x-data="{
        show: false,
        current: $wire.entangle('current').live,
        init() {
            this.$watch('show', val => document.body.style.overflow = val ? 'hidden' : '');
            this.$watch('current', val => { if (val) this.show = true; });
        },
        close() {
            this.show = false;
            // Reset server state only after the slide-out finishes, so the
            // content doesn't flash back to the skeleton mid-transition.
            setTimeout(() => $wire.close(), 200);
        },
    }"
    @open-modal.window="show = true"
    @close-modal.window="show = false"
    x-on:livewire:navigating.window="show = false; document.body.style.overflow = ''"
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
        @click="close()"
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
        @keydown.escape.window="close()"
        class="fixed inset-y-0 right-0 z-50 flex flex-col w-full md:w-[520px] bg-bg shadow-2xl"
        role="dialog"
        aria-modal="true"
        :aria-label="$wire.title"
    >
        <div class="flex items-center justify-between px-6 py-5 border-b border-dark/10 shrink-0">
            <h2 x-show="current" class="font-heading text-xl text-heading">{{ $title }}</h2>
            <div x-show="!current" class="skeleton h-5 w-32 rounded-full" aria-hidden="true"></div>

            <button
                @click="close()"
                type="button"
                class="w-9 h-9 flex items-center justify-center rounded-full text-caption hover:text-body hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
                aria-label="{{ __('Fermer') }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5" aria-hidden="true">
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Loading state: accent progress sweep + breathing content skeleton --}}
        <div x-show="!current" class="flex-1 overflow-hidden" role="status" aria-busy="true">
            <span class="sr-only">{{ __('Chargement du contenu') }}</span>
            <div class="modal-progress" aria-hidden="true"></div>

            <div class="p-6 space-y-7" aria-hidden="true">
                @foreach ([['h-3.5 w-1/3', 'h-3 w-3/4'], ['h-3.5 w-2/5', 'h-3 w-2/3'], ['h-3.5 w-1/4', 'h-3 w-4/5'], ['h-3.5 w-1/3', 'h-3 w-1/2']] as $i => [$head, $line])
                    <div class="flex items-center gap-4">
                        <div class="skeleton h-12 w-12 rounded-2xl shrink-0" style="animation-delay: {{ $i * 80 }}ms"></div>
                        <div class="flex-1 space-y-2.5">
                            <div class="skeleton {{ $head }} rounded-full" style="animation-delay: {{ $i * 80 + 40 }}ms"></div>
                            <div class="skeleton {{ $line }} rounded-full" style="animation-delay: {{ $i * 80 + 80 }}ms"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Loaded content --}}
        <div x-show="current" class="flex-1 overflow-y-auto p-6">
            @if (! is_null($current))
                <livewire:dynamic-component
                    :is="$current"
                    :wire:key="$key"
                    :model_id="$model_id"
                    :media_id="$media_id"
                />
            @endif
        </div>
    </section>
</div>
