<?php

use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public bool $show = false;

    #[On('open-auth-modal')]
    public function open(): void
    {
        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
    }
};
?>

<div
    x-data="{ show: $wire.entangle('show').live }"
    @open-auth-modal.window="show = true"
    x-show="show"
    @keydown.escape.window="if (show) show = false"
    class="fixed inset-0 z-[60] flex items-center justify-center p-4"
    style="display: none"
    role="dialog"
    aria-modal="true"
    aria-labelledby="auth-modal-title"
>
    {{-- Backdrop --}}
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="show = false"
        class="fixed inset-0 bg-dark/55 backdrop-blur-sm"
        aria-hidden="true"
    ></div>

    {{-- Card --}}
    <div
        x-show="show"
        x-transition:enter="transition ease-[cubic-bezier(0.34,1.56,0.64,1)] duration-300"
        x-transition:enter-start="opacity-0 scale-90 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        class="relative z-10 w-full max-w-sm rounded-2xl overflow-hidden shadow-2xl"
    >
        {{-- ── Dark header ── --}}
        <div class="relative bg-dark px-8 pt-9 pb-8 flex flex-col items-center gap-5 overflow-hidden">

            {{-- Spotlight radial glow --}}
            <div
                class="absolute inset-0 pointer-events-none"
                style="background: radial-gradient(ellipse 70% 80% at 50% -10%, rgba(246,118,73,.28) 0%, transparent 65%)"
                aria-hidden="true"
            ></div>

            {{-- Close --}}
            <button
                wire:click="close"
                type="button"
                class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full text-bg/35 hover:text-bg hover:bg-white/10 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent/60"
                aria-label="{{ __('Fermer') }}"
            >
                <x-icon name="x-mark" class="w-4 h-4" />
            </button>

            {{-- Logo --}}
            <x-logo class="h-7 w-auto my-10 text-bg" />
        </div>

        {{-- ── Body ── --}}
        <section class="bg-bg px-8 pt-7 pb-8">
            <h2
                id="auth-modal-title"
                class="font-heading text-[1.35rem] leading-snug text-dark text-center"
            >
                {{ __('Rejoins la communauté.') }}
            </h2>
            <p class="mt-2.5 text-sm text-dark/50 text-center leading-relaxed">
                {{ __('Connecte-toi pour accéder aux profils et annonces de musiciens.') }}
            </p>

            <div class="mt-6 flex flex-col gap-3">
                <x-cta
                    variant="dark"
                    href="{{ route('login') }}"
                    wire:navigate
                    size="lg"
                    @click="show = false"
                    class="w-full gap-2"
                >
                    {{ __('Se connecter') }}
                </x-cta>
                <x-cta
                    variant="outline"
                    href="{{ route('register') }}"
                    wire:navigate
                    size="lg"
                    @click="show = false"
                    class="w-full"
                >
                    {{ __('Créer un compte') }}
                </x-cta>
            </div>

            <p class="mt-5 text-center text-xs text-dark/30">
                {{ __('En continuant, tu acceptes les') }}
                <span class="underline underline-offset-2 cursor-default">{{ __('conditions d\'utilisation') }}</span>
            </p>
        </section>
    </div>
</div>
