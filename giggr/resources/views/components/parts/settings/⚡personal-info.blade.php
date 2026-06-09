<?php

use Livewire\Component;

new class extends Component {
    public ?string $birth_date = null;

    public ?int $cityId = null;

    public bool $saved = false;

    public function mount(): void
    {
        abort_unless(auth()->check(), 403);

        $profile = auth()->user()->profile;

        $this->birth_date = $profile?->birth_date?->format('Y-m-d');
        $this->cityId = $profile?->city_id;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'birth_date' => ['nullable', 'date_format:Y-m-d', 'before:today', 'after:1900-01-01'],
            'cityId' => ['nullable', 'integer', 'exists:cities,id'],
        ]);

        auth()->user()->profile->update([
            'birth_date' => $validated['birth_date'] ?? null,
            'city_id' => $validated['cityId'] ?? null,
        ]);

        $this->saved = true;
    }

    public function updatedBirthDate(): void
    {
        $this->saved = false;
    }

    public function updatedCityId(): void
    {
        $this->saved = false;
    }
};
?>

<x-settings.section
    labelledby="settings-personal-heading"
    :title="__('settings.personal_title')"
    :description="__('settings.personal_description')"
>
    <form wire:submit="save" class="space-y-4" novalidate>
        <x-form.input
            name="birth_date"
            type="date"
            :label="__('settings.birth_date_label')"
            wire:model="birth_date"
            :value="$birth_date"
            autocomplete="bday"
            :max="now()->format('Y-m-d')"
        />

        <div>
            <livewire:parts.form.locality-picker
                wire:model.live="cityId"
                :label="__('settings.city_label')"
                :required="false"
            />
            @error('cityId')
            <p class="text-xs text-accent mt-1.5" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between gap-4 pt-1">
            <p
                @class(['text-sm text-accent', 'invisible' => ! $saved])
                role="status"
                aria-live="polite"
            >
                {{ __('settings.personal_saved') }}
            </p>
            <x-cta type="submit" variant="dark" wire:loading.attr="disabled">
                {{ __('settings.personal_submit') }}
            </x-cta>
        </div>
    </form>
</x-settings.section>
