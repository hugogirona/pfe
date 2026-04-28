<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.auth')] #[Title('Mot de passe oublié — Giggr.')] class extends Component
{
    //
};
?>

<div x-data="{ sent: false }">
    <div x-show="!sent">
        <div class="mb-8">
            <h1 id="forgot-heading" class="font-heading text-3xl text-dark mb-1.5">{{ __('auth.forgot_heading') }}</h1>
            <p class="text-sm text-dark/50">{{ __('auth.forgot_subtitle') }}</p>
        </div>
        <form
            @submit.prevent="sent = true"
            novalidate
            aria-labelledby="forgot-heading"
            class="space-y-5"
        >
            @csrf
            <x-form.input
                name="email"
                type="email"
                :label="__('auth.email_label')"
                :required="true"
                autocomplete="email"
                :placeholder="__('auth.email_placeholder')"
            />
            <x-cta type="submit" size="lg" class="w-full min-h-[44px]">
                {{ __('auth.forgot_submit') }}
            </x-cta>
        </form>
    </div>

    <div x-show="sent" x-cloak>
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
    </div>

    <p class="text-sm text-dark/50 mt-6" x-show="!sent">
        <a href="{{ route('login') }}"
           class="text-dark font-medium underline underline-offset-2
                  hover:text-accent transition-colors duration-150
                  focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-sm">
            {{ __('auth.back_to_login') }}
        </a>
    </p>

</div>
