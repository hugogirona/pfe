<?php

use App\Models\Profile;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public Profile $profile;
    public ?TemporaryUploadedFile $photo = null;

    public function mount(?string $model_id = null): void
    {
        $this->profile = Profile::findOrFail($model_id);
    }

    public function save(): void
    {
        abort_unless(auth()->id() === $this->profile->user_id, 403);

        $this->validate([
            'photo' => [
                'required',
                'image',
                'max:' . config('avatars.max_file_size'),
                'mimes:jpeg,jpg,png,webp,gif',
            ],
        ]);

        $this->profile->processAvatar($this->photo);

        $this->dispatch('avatar-saved', thumbnail: $this->profile->refresh()->thumbnail);
        $this->dispatch('close-modal');
    }
};
?>

<div>
    <form wire:submit="save" class="space-y-6" novalidate>
        <div
            x-data="{ dragging: false }"
            @dragover.prevent="dragging = true"
            @dragleave.prevent="dragging = false"
            @drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
            class="relative"
        >
            <label
                for="avatar-upload"
                :class="dragging ? 'border-accent bg-accent/5' : 'border-dark/15 hover:border-dark/30'"
                class="flex flex-col items-center justify-center gap-3 w-full min-h-48 rounded-xl border-2 border-dashed transition-colors duration-150 cursor-pointer overflow-hidden"
            >
                <div class="flex flex-col items-center gap-2 px-6 py-8 text-center">
                    @if ($photo && $photo->isPreviewable())
                        <img
                            src="{{ $photo->temporaryUrl() }}"
                            alt="{{ __('profile.avatar_alt', ['name' => $profile->user->full_name]) }}"
                            class="w-36 h-36 rounded-full object-cover object-center mb-1 ring-2 ring-dark/10"
                        />
                    @elseif ($profile->medium)
                        <img
                            src="{{ $profile->medium }}"
                            alt="{{ __('profile.avatar_alt', ['name' => $profile->user->full_name]) }}"
                            class="w-36 h-36 rounded-full object-cover object-center mb-1 ring-2 ring-dark/10"
                        />
                    @else
                        <div class="w-36 h-36 rounded-full bg-pastel-blue flex items-center justify-center mb-1">
                            <span class="font-heading text-4xl text-dark/30 select-none">
                                {{ mb_substr($profile->user->full_name, 0, 1) }}
                            </span>
                        </div>
                    @endif
                    <p class="text-sm text-dark/60">{{ __('profile.avatar_drop') }}</p>
                </div>
            </label>

            <input
                x-ref="fileInput"
                id="avatar-upload"
                type="file"
                wire:model="photo"
                accept="image/jpeg,image/png,image/webp,image/gif"
                class="sr-only"
            />
        </div>

        <p class="text-xs text-dark/40 text-center -mt-3">{{ __('profile.avatar_hint') }}</p>

        @error('photo')
        <p class="text-xs text-danger mt-1" role="alert">{{ $message }}</p>
        @enderror

        {{-- Submit --}}
        <div class="flex justify-end pt-2 border-t border-dark/10">
            <x-cta
                type="submit"
                variant="dark"
                size="base"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-not-allowed"
            >
                <span wire:loading.remove>{{ __('profile.save') }}</span>
                <span wire:loading class="flex items-center gap-2">
                    {{ __('profile.save') }}
                </span>
            </x-cta>
        </div>
    </form>
</div>
