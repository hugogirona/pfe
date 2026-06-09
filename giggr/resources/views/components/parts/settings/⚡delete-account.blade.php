<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component {
    public string $current_password = '';

    public function mount(): void
    {
        abort_unless(auth()->check(), 403);
    }

    public function delete(): void
    {
        $this->validate([
            'current_password' => ['required', 'string', 'current_password:web'],
        ], [
            'current_password.current_password' => __('settings.current_password_mismatch'),
        ]);
        $user = auth()->user();
        Auth::guard('web')->logout();
        $user->delete();
        session()->invalidate();
        session()->regenerateToken();
        $this->redirect(route('home'));
    }
};
?>

<x-settings.section
    labelledby="settings-delete-heading"
    :title="__('settings.delete_title')"
    :description="__('settings.delete_description')"
    class="border-danger/30"
    x-data="{ confirming: false }"
>
    <div x-show="!confirming">
        <x-cta variant="danger-solid" type="button" x-on:click="confirming = true">
            {{ __('settings.delete_button') }}
        </x-cta>
    </div>

    <form wire:submit="delete" novalidate x-cloak x-show="confirming" class="space-y-4">
        <p class="text-sm text-danger font-medium">{{ __('settings.delete_warning') }}</p>

        <div>
            <x-form.password
                name="current_password"
                id="delete_current_password"
                :label="__('settings.current_password_label')"
                wire:model="current_password"
                :required="true"
                autocomplete="current-password"
            />
            @error('current_password')
            <p class="text-xs text-danger mt-1" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end gap-3 pt-1">
            <x-cta
                variant="simple"
                type="button"
                x-on:click="confirming = false; $wire.set('current_password', '')"
            >
                {{ __('settings.delete_cancel') }}
            </x-cta>
            <x-cta variant="danger-solid" type="submit" wire:loading.attr="disabled">
                {{ __('settings.delete_confirm') }}
            </x-cta>
        </div>
    </form>
</x-settings.section>
