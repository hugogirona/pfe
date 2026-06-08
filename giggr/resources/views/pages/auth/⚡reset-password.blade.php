<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.auth')] class extends Component
{
    public string $token = '';
    public string $email = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return $this->view()->title(__('titles.password.reset'));
    }
};
?>

<div>

    <div class="mb-8">
        <h1 id="reset-heading" class="font-heading text-3xl text-heading mb-1.5">{{ __('auth.reset_heading') }}</h1>
        <p class="text-sm text-subtle">{{ __('auth.reset_subtitle') }}</p>
    </div>

    <form action="/reset-password" method="POST" novalidate aria-labelledby="reset-heading" class="space-y-5">
        @csrf
        <x-honeypot/>

        <input type="hidden" name="token" value="{{ $token }}">

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

        <p class="text-xs text-caption">
            {{ __('auth.required_legend') }}
        </p>

        <x-cta type="submit" size="lg" class="w-full min-h-[44px]">
            {{ __('auth.reset_submit') }}
        </x-cta>

    </form>

    <p class="text-sm text-subtle mt-6">
        <a href="{{ route('login') }}"
           class="text-body font-medium underline underline-offset-2
                  hover:text-accent transition-colors duration-150
                  focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-sm">
            {{ __('auth.back_to_login') }}
        </a>
    </p>

</div>
