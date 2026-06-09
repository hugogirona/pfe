<?php

use App\Enums\ContactPreference;
use App\Events\ContactPreferenceUpdated;
use App\Events\ConversationClosed;
use App\Models\Conversation;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component {
    public string $contactPreference = '';

    public bool $saved = false;

    public function mount(): void
    {
        abort_unless(auth()->check(), 403);

        $this->contactPreference = (auth()->user()->profile?->contact_preference ?? ContactPreference::Everyone)->value;
    }

    public function updatedContactPreference(string $value): void
    {
        $this->validate([
            'contactPreference' => [Rule::enum(ContactPreference::class)],
        ]);

        $profile = auth()->user()->profile;
        $profile->update(['contact_preference' => $value]);

        $this->lockPendingContacts();

        try {
            broadcast(new ContactPreferenceUpdated((int) $profile->id));
        } catch (\Throwable $e) {
            report($e);
        }

        $this->saved = true;
    }

    private function lockPendingContacts(): void
    {
        $me = auth()->user();

        $me->conversations()
            ->whereNull('accepted_at')
            ->with('participants')
            ->get()
            ->each(function (Conversation $conversation) use ($me): void {
                $other = $conversation->participants->firstWhere('id', '!=', $me->id);
                if ($other !== null && ! $me->canBeContactedBy($other)) {
                    try {
                        broadcast(new ConversationClosed((int) $conversation->id));
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }
            });
    }
};
?>

<x-settings.section
    labelledby="settings-privacy-heading"
    :title="__('settings.privacy_title')"
    :description="__('settings.privacy_description')"
>
    <fieldset class="min-w-0">
        <legend class="sr-only">{{ __('settings.privacy_title') }}</legend>

        <div class="space-y-3">
            <x-form.radio
                name="contact_preference"
                :value="\App\Enums\ContactPreference::Everyone->value"
                :label="__('settings.contact_everyone_label')"
                :description="__('settings.contact_everyone_description')"
                wire:model.live="contactPreference"
            />
            <x-form.radio
                name="contact_preference"
                :value="\App\Enums\ContactPreference::FollowersOnly->value"
                :label="__('settings.contact_followers_label')"
                :description="__('settings.contact_followers_description')"
                wire:model.live="contactPreference"
            />
            <x-form.radio
                name="contact_preference"
                :value="\App\Enums\ContactPreference::Nobody->value"
                :label="__('settings.contact_nobody_label')"
                :description="__('settings.contact_nobody_description')"
                wire:model.live="contactPreference"
            />
        </div>
    </fieldset>

    <p
        @class(['text-sm text-success mt-4', 'invisible' => ! $saved])
        role="status"
        aria-live="polite"
        wire:key="privacy-saved"
    >
        {{ __('settings.privacy_saved') }}
    </p>
</x-settings.section>
