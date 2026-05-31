<?php

use App\Actions\UpdateUserEmail;
use Livewire\Component;

new class extends Component {
    public string $email = '';

    public string $current_password = '';

    public function mount(): void
    {
        abort_unless(auth()->check(), 403);

        $this->email = auth()->user()->email;
    }

    public function save(): void
    {
        app(UpdateUserEmail::class)->update(auth()->user(), [
            'email' => $this->email,
            'current_password' => $this->current_password,
        ]);

        $this->redirect(route('verification.notice'), navigate: true);
    }
};
?>

<x-settings.section
    labelledby="settings-email-heading"
    :title="__('settings.email_title')"
    :description="__('settings.email_description')"
>
    <form wire:submit="save" class="space-y-4" novalidate>
        <x-form.input
            name="email"
            type="email"
            :label="__('settings.email_label')"
            wire:model="email"
            :value="$email"
            :required="true"
            autocomplete="email"
        />

        <div>
            <x-form.password
                name="current_password"
                :label="__('settings.current_password_label')"
                wire:model="current_password"
                :required="true"
                autocomplete="current-password"
            />
            @error('current_password')
                <p class="text-xs text-danger mt-1" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end pt-1">
            <x-cta type="submit" variant="dark" wire:loading.attr="disabled">
                {{ __('settings.email_submit') }}
            </x-cta>
        </div>
    </form>
</x-settings.section>
