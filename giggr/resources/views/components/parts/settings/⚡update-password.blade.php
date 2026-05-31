<?php

use App\Actions\Fortify\PasswordValidationRules;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

new class extends Component {
    use PasswordValidationRules;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $saved = false;

    public function mount(): void
    {
        abort_unless(auth()->check(), 403);
    }

    public function save(): void
    {
        $this->validate([
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => [...$this->passwordRules(), 'confirmed'],
        ], [
            'current_password.current_password' => __('settings.current_password_mismatch'),
        ]);

        auth()->user()->forceFill([
            'password' => Hash::make($this->password),
        ])->save();

        $this->reset('current_password', 'password', 'password_confirmation');
        $this->saved = true;
    }

    public function updated(): void
    {
        $this->saved = false;
    }
};
?>

<x-settings.section
    labelledby="settings-password-heading"
    :title="__('settings.password_title')"
    :description="__('settings.password_description')"
>
    <form wire:submit="save" class="space-y-4" novalidate>
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

        <div>
            <x-form.password
                name="password"
                :label="__('settings.new_password_label')"
                wire:model="password"
                :required="true"
                autocomplete="new-password"
            />
            @error('password')
                <p class="text-xs text-danger mt-1" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <x-form.password
            name="password_confirmation"
            :label="__('settings.confirm_password_label')"
            wire:model="password_confirmation"
            :required="true"
            autocomplete="new-password"
        />

        <div class="flex items-center justify-between gap-4 pt-1">
            <p
                @class(['text-sm text-accent', 'invisible' => ! $saved])
                role="status"
                aria-live="polite"
            >
                {{ __('settings.password_saved') }}
            </p>
            <x-cta type="submit" variant="dark" wire:loading.attr="disabled">
                {{ __('settings.password_submit') }}
            </x-cta>
        </div>
    </form>
</x-settings.section>
