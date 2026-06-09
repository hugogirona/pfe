<?php

use Livewire\Component;

new class extends Component {
    public ?string $birth_date = null;

    public ?int $cityId = null;

    public ?int $experienceYears = null;

    public bool $saved = false;

    public function mount(): void
    {
        abort_unless(auth()->check(), 403);

        $profile = auth()->user()->profile;

        $this->birth_date = $profile?->birth_date?->format('Y-m-d');
        $this->cityId = $profile?->city_id;

        $years = $profile?->experience_years ?? 0;
        $this->experienceYears = $years > 0 ? min($years, 15) : null;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'birth_date' => ['nullable', 'date_format:Y-m-d', 'before:today', 'after:1900-01-01'],
            'cityId' => ['nullable', 'integer', 'exists:cities,id'],
            'experienceYears' => ['nullable', 'integer', 'between:1,15'],
        ]);

        auth()->user()->profile->update([
            'birth_date' => $validated['birth_date'] ?? null,
            'city_id' => $validated['cityId'] ?? null,
            'experience_years' => $validated['experienceYears'] ?? 0,
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

    public function updatedExperienceYears(): void
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

        <x-form.select
            name="experienceYears"
            :label="__('settings.experience_label')"
            wire:model="experienceYears"
        >
            <option value="">{{ __('settings.experience_unset') }}</option>
            @for ($i = 1; $i < 15; $i++)
                <option value="{{ $i }}">{{ trans_choice('settings.experience_option', $i) }}</option>
            @endfor
            <option value="15">{{ __('settings.experience_max') }}</option>
        </x-form.select>

        <div class="flex items-center justify-between gap-4 pt-1">
            <p
                @class(['text-sm text-success', 'invisible' => ! $saved])
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
