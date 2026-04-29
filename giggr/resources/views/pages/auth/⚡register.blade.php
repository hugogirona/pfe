<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.auth')] #[Title('Créer un compte — Giggr.')] class extends Component
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
        <h1 id="register-heading" class="font-heading text-3xl text-dark mb-1.5">{{ __('auth.register_heading') }}</h1>
        <p class="text-sm text-dark/50">{{ __('auth.register_subtitle') }}</p>
    </div>

    <form action="/register" method="POST" novalidate aria-labelledby="register-heading" class="space-y-5">
        @csrf

        @if ($errors->any())
            <div class="rounded-[6px] bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                <ul class="space-y-1 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{!! $error !!}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <x-form.input
                name="first_name"
                type="text"
                :label="__('auth.register_first_name')"
                :required="true"
                autocomplete="given-name"
                :placeholder="__('auth.register_first_name_ph')"
            />
            <x-form.input
                name="last_name"
                type="text"
                :label="__('auth.register_last_name')"
                :required="true"
                autocomplete="family-name"
                :placeholder="__('auth.register_last_name_ph')"
            />
        </div>

        <x-form.input
            name="email"
            type="email"
            :label="__('auth.email_label')"
            :required="true"
            autocomplete="email"
            :placeholder="__('auth.email_placeholder')"
        />

        <x-form.password
            name="password"
            :label="__('auth.password_label')"
            :required="true"
            autocomplete="new-password"
            :placeholder="__('auth.register_password_ph')"
        />

        <p class="text-xs text-dark/40">
            {{ __('auth.required_legend') }}
        </p>

        <x-cta type="submit" size="lg" class="w-full min-h-[44px]">
            {{ __('auth.register_submit') }}
        </x-cta>

    </form>

    <p class="text-sm text-dark/50 mt-6">
        {{ __('auth.register_login_prompt') }}
        <a href="{{ route('login') }}"
           class="text-dark font-medium underline underline-offset-2
                  hover:text-accent transition-colors duration-150
                  focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-sm">
            {{ __('auth.register_login_link') }}
        </a>
    </p>

</div>
