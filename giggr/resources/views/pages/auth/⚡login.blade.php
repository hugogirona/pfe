<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.auth')] #[Title('Se connecter — Giggr.')] class extends Component
{
    public function mount(): void
    {
        if (auth()->check()) {
            $this->redirect(config('fortify.home'), navigate: true);
        }
    }
};
?>

<div>

    <div class="mb-8">
        <h1 id="login-heading" class="font-heading text-3xl text-dark mb-1.5">{{ __('auth.login_heading') }}</h1>
        <p class="text-sm text-dark/50">{{ __('auth.login_subtitle') }}</p>
    </div>

    <form action="/login" method="POST" novalidate aria-labelledby="login-heading" class="space-y-5">
        @csrf

        @if ($errors->any())
            <div class="rounded-[6px] bg-danger/5 border border-danger/50 px-4 py-3 text-sm text-danger/70">
                {{ $errors->first() }}
            </div>
        @endif

        <x-form.input
            name="email"
            type="email"
            :label="__('auth.email_label')"
            :required="true"
            autocomplete="email"
            :placeholder="__('auth.email_placeholder')"
            :value="old('email')"
        />

        <x-form.password
            name="password"
            :label="__('auth.password_label')"
            :required="true"
            autocomplete="current-password"
        />

        <div class="flex justify-end -mt-2">
            <a href="{{ route('password.request') }}"
               class="text-xs text-dark/50 hover:text-accent transition-colors duration-150
                      focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-sm">
                {{ __('auth.login_forgot') }}
            </a>
        </div>

        <x-cta type="submit" size="lg" class="w-full min-h-[44px]">
            {{ __('auth.login_submit') }}
        </x-cta>

    </form>

    <p class="text-sm text-dark/50 mt-6">
        {{ __('auth.login_register_prompt') }}
        <a href="{{ route('register') }}"
           class="text-dark font-medium underline underline-offset-2
                  hover:text-accent transition-colors duration-150
                  focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-sm">
            {{ __('auth.login_register_link') }}
        </a>
    </p>

</div>
