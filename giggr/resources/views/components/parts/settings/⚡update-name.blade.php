<?php

use Livewire\Component;

new class extends Component {
    public string $first_name = '';

    public string $last_name = '';

    public bool $saved = false;

    public function mount(): void
    {
        abort_unless(auth()->check(), 403);

        $user = auth()->user();
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
        ]);

        auth()->user()->update($validated);

        $this->saved = true;
    }

    public function updatedFirstName(): void
    {
        $this->saved = false;
    }

    public function updatedLastName(): void
    {
        $this->saved = false;
    }
};
?>

<x-settings.section
    labelledby="settings-name-heading"
    :title="__('settings.name_title')"
    :description="__('settings.name_description')"
>
    <form wire:submit="save" class="space-y-4" novalidate>
        <x-form.input
            name="first_name"
            :label="__('settings.name_first_label')"
            wire:model="first_name"
            :value="$first_name"
            :required="true"
            autocomplete="given-name"
        />

        <x-form.input
            name="last_name"
            :label="__('settings.name_last_label')"
            wire:model="last_name"
            :value="$last_name"
            :required="true"
            autocomplete="family-name"
        />

        <div class="flex items-center justify-between gap-4 pt-1">
            <p
                @class(['text-sm text-success', 'invisible' => ! $saved])
                role="status"
                aria-live="polite"
            >
                {{ __('settings.name_saved') }}
            </p>
            <x-cta type="submit" variant="dark" wire:loading.attr="disabled">
                {{ __('settings.name_submit') }}
            </x-cta>
        </div>
    </form>
</x-settings.section>
