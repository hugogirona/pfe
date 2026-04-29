<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.auth')] #[Title('Mot de passe oublié — Giggr.')] class extends Component
{
    public bool $sent = false;

    public function mount(): void
    {
        $this->sent = session('status') === 'passwords.sent';
    }
};
?>

<div>
    @if (!$sent)
        <div class="mb-8">
            <h1 id="forgot-heading" class="font-heading text-3xl text-dark mb-1.5">{{ __('auth.forgot_heading') }}</h1>
            <p class="text-sm text-dark/50">{{ __('auth.forgot_subtitle') }}</p>
        </div>
        <form action="/forgot-password" method="POST" novalidate aria-labelledby="forgot-heading" class="space-y-5">
            @csrf

            @if ($errors->any())
                <div class="rounded-[6px] bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
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
            <x-cta type="submit" size="lg" class="w-full min-h-[44px]">
                {{ __('auth.forgot_submit') }}
            </x-cta>
        </form>

        <p class="text-sm text-dark/50 mt-6">
            <a href="{{ route('login') }}"
               class="text-dark font-medium underline underline-offset-2
                      hover:text-accent transition-colors duration-150
                      focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-sm">
                {{ __('auth.back_to_login') }}
            </a>
        </p>
    @else
        <div class="mb-8">
            <h1 class="font-heading text-3xl text-dark mb-1.5">{{ __('auth.forgot_sent_heading') }}</h1>
            <p class="text-sm text-dark/50">{{ __('auth.forgot_sent_subtitle') }}</p>
        </div>
        <div class="rounded-[6px] bg-dark/5 border border-dark/10 px-5 py-4 text-sm text-dark/70 leading-relaxed">
            {{ __('auth.forgot_sent_spam') }}
        </div>
        <div class="mt-5">
            <x-cta href="{{ route('login') }}" size="lg" class="w-full min-h-[44px]">
                {{ __('auth.forgot_sent_back') }}
            </x-cta>
        </div>
    @endif
</div>
