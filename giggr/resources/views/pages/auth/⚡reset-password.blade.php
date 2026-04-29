<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.auth')] #[Title('Réinitialiser le mot de passe — Giggr.')] class extends Component
{
    public string $token = '';
    public string $email = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }
};
?>

<div>

    <div class="mb-8">
        <h1 id="reset-heading" class="font-heading text-3xl text-dark mb-1.5">{{ __('auth.reset_heading') }}</h1>
        <p class="text-sm text-dark/50">{{ __('auth.reset_subtitle') }}</p>
    </div>

    <form action="/reset-password" method="POST" novalidate aria-labelledby="reset-heading" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        @if ($errors->any())
            <div class="rounded-[6px] bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                <ul class="space-y-1 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <x-form.input
            name="email"
            type="email"
            :label="__('auth.email_label')"
            :required="true"
            autocomplete="email"
            :placeholder="__('auth.email_placeholder')"
            :value="$email ?: old('email')"
        />

        <x-form.password
            name="password"
            :label="__('auth.reset_new_password')"
            :required="true"
            autocomplete="new-password"
            :placeholder="__('auth.reset_new_password_ph')"
        />

        <x-form.password
            name="password_confirmation"
            :label="__('auth.reset_confirm_password')"
            :required="true"
            autocomplete="new-password"
            :placeholder="__('auth.reset_confirm_password_ph')"
        />

        <p class="text-xs text-dark/40">
            {{ __('auth.required_legend') }}
        </p>

        <x-cta type="submit" size="lg" class="w-full min-h-[44px]">
            {{ __('auth.reset_submit') }}
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

</div>
