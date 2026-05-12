<?php

use App\Actions\UploadMediaImage;
use App\Models\Profile;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public ?string $model_id = null;

    public ?TemporaryUploadedFile $photo = null;

    public string $caption = '';

    public bool $success = false;

    public function mount(?string $model_id = null): void
    {
        $this->model_id = $model_id;
    }

    public function save(): void
    {
        abort_unless(auth()->check(), 403);

        $profile = Profile::findOrFail((int) $this->model_id);
        abort_unless(auth()->id() === $profile->user_id, 403);

        $this->validate([
            'photo' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:'.config('media.max_file_size'),
            ],
            'caption' => ['nullable', 'string', 'max:255'],
        ]);

        $cap = (int) config('media.max_per_profile');
        if ($profile->media()->count() >= $cap) {
            $this->addError('photo', __('profile.media_cap_reached', ['max' => $cap]));

            return;
        }

        app(UploadMediaImage::class)->execute(
            $profile,
            $this->photo,
            $this->caption !== '' ? $this->caption : null,
        );

        $this->dispatch('media-added');
        $this->success = true;
    }

    public function close(): void
    {
        $this->dispatch('close-modal');
    }
};
?>

<div>
@if ($success)
    <div class="flex flex-col items-center gap-4 py-4 text-center">
        <div class="w-14 h-14 rounded-full bg-pastel-salmon flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7 text-accent" aria-hidden="true">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
        </div>
        <div>
            <h3 class="font-heading text-xl text-dark">{{ __('profile.add_image_success_title') }}</h3>
            <p class="text-sm text-dark/60 mt-1">{{ __('profile.add_image_success_body') }}</p>
        </div>
        <button
            wire:click="close"
            type="button"
            class="h-11 px-6 rounded-md bg-dark text-bg text-sm font-medium hover:opacity-80 transition-opacity duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
        >
            {{ __('profile.add_image_close') }}
        </button>
    </div>
@else
    <form wire:submit="save" class="space-y-5" novalidate>

        <div
            x-data="{ dragging: false }"
            @dragover.prevent="dragging = true"
            @dragleave.prevent="dragging = false"
            @drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
            class="relative"
        >
            <label
                for="media-image-upload"
                :class="dragging ? 'border-accent bg-accent/5' : 'border-dark/15 hover:border-dark/30'"
                class="flex flex-col items-center justify-center gap-3 w-full min-h-56 rounded-xl border-2 border-dashed transition-colors duration-150 cursor-pointer overflow-hidden"
            >
                <div class="flex flex-col items-center gap-2 px-6 py-8 text-center">
                    @if ($photo && $photo->isPreviewable())
                        <img
                            src="{{ $photo->temporaryUrl() }}"
                            alt=""
                            class="max-w-full max-h-48 rounded-md object-contain ring-1 ring-dark/10"
                        />
                    @else
                        <x-icon name="photo" class="w-10 h-10 text-dark/30"/>
                        <p class="text-sm text-dark/60">{{ __('profile.add_image_drop') }}</p>
                    @endif
                </div>
            </label>

            <input
                x-ref="fileInput"
                id="media-image-upload"
                type="file"
                wire:model="photo"
                accept="image/jpeg,image/png,image/webp"
                class="sr-only"
            />
        </div>

        <p class="text-xs text-dark/40 text-center -mt-3">{{ __('profile.add_image_hint') }}</p>

        @error('photo')
            <p class="text-xs text-accent mt-1" role="alert">{{ $message }}</p>
        @enderror

        <div>
            <x-form.input
                name="caption"
                :label="__('profile.add_image_caption_label')"
                wire:model="caption"
                :placeholder="__('profile.add_image_caption_placeholder')"
            />
            @error('caption')
                <p class="text-xs text-accent mt-1.5" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end pt-2 border-t border-dark/10">
            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-not-allowed"
                class="h-11 px-6 rounded-md bg-dark text-bg text-sm font-medium hover:opacity-80 transition-opacity duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
            >
                <span wire:loading.remove wire:target="save,photo">{{ __('profile.add_image_submit') }}</span>
                <span wire:loading wire:target="save,photo">{{ __('profile.add_image_submitting') }}</span>
            </button>
        </div>

    </form>
@endif
</div>
