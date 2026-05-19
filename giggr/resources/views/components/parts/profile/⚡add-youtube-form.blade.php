<?php

use App\Enums\MediaType;
use App\Models\Media;
use App\Models\Profile;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public ?string $model_id = null;

    public ?string $media_id = null;

    #[Validate('required|string|regex:/^[A-Za-z0-9_-]{11}$/')]
    public string $videoId = '';

    #[Validate('nullable|string|max:255')]
    public string $caption = '';

    public bool $success = false;

    public bool $isEdit = false;

    public function mount(?string $model_id = null, ?string $media_id = null): void
    {
        $this->model_id = $model_id;
        $this->media_id = $media_id;

        if ($media_id !== null) {
            $media = Media::findOrFail((int) $media_id);
            $this->isEdit = true;
            $this->model_id = (string) $media->profile_id;
            $this->videoId = $media->source;
            $this->caption = $media->caption ?? '';
        }
    }

    public function save(): void
    {
        abort_unless(auth()->check(), 403);

        $profile = Profile::findOrFail((int) $this->model_id);
        abort_unless(auth()->id() === $profile->user_id, 403);

        $this->validate();

        $captionValue = $this->caption !== '' ? $this->caption : null;

        if ($this->isEdit) {
            $media = Media::findOrFail((int) $this->media_id);
            abort_unless($media->profile_id === $profile->id, 403);

            $duplicate = $profile->media()
                ->where('type', MediaType::Youtube->value)
                ->where('source', $this->videoId)
                ->where('id', '!=', $media->id)
                ->exists();
            if ($duplicate) {
                $this->addError('videoId', __('profile.media_youtube_duplicate'));

                return;
            }

            $media->update([
                'source' => $this->videoId,
                'caption' => $captionValue,
            ]);

            $this->dispatch('media-updated');
            $this->success = true;

            return;
        }

        $cap = (int) config('media.max_per_profile');
        if ($profile->media()->count() >= $cap) {
            $this->addError('videoId', __('profile.media_cap_reached', ['max' => $cap]));

            return;
        }

        $duplicate = $profile->media()
            ->where('type', MediaType::Youtube->value)
            ->where('source', $this->videoId)
            ->exists();
        if ($duplicate) {
            $this->addError('videoId', __('profile.media_youtube_duplicate'));

            return;
        }

        Media::create([
            'profile_id' => $profile->id,
            'type' => MediaType::Youtube,
            'source' => $this->videoId,
            'caption' => $captionValue,
            'position' => ($profile->media()->max('position') ?? -1) + 1,
        ]);

        $this->dispatch('media-added');
        $this->success = true;
    }

    public function delete(): void
    {
        abort_unless(auth()->check() && $this->media_id !== null, 403);

        $media = Media::findOrFail((int) $this->media_id);
        abort_unless(auth()->id() === $media->profile->user_id, 403);

        $media->delete();

        $this->dispatch('media-deleted');
        $this->dispatch('close-modal');
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
            <h3 class="font-heading text-xl text-dark">
                {{ $isEdit ? __('profile.update_youtube_success_title') : __('profile.add_youtube_success_title') }}
            </h3>
            <p class="text-sm text-dark/60 mt-1">
                {{ $isEdit ? __('profile.update_youtube_success_body') : __('profile.add_youtube_success_body') }}
            </p>
        </div>
        <button
            wire:click="close"
            type="button"
            class="h-11 px-6 rounded-md bg-dark text-bg text-sm font-medium hover:opacity-80 transition-opacity duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
        >
            {{ __('profile.add_youtube_close') }}
        </button>
    </div>
@else
    <form wire:submit="save" class="space-y-5" novalidate>

        <div class="bg-pastel-blue/40 border border-pastel-blue rounded-md p-4 text-sm text-dark/75 leading-relaxed">
            <p class="font-medium text-dark mb-1">{{ __('profile.add_youtube_help_title') }}</p>
            <p>
                {!! __('profile.add_youtube_help_body') !!}
            </p>
        </div>

        <div>
            <x-form.input
                name="videoId"
                :label="__('profile.add_youtube_label')"
                wire:model="videoId"
                :placeholder="__('profile.add_youtube_placeholder')"
                required
            />
            @error('videoId')
                <p class="text-xs text-accent mt-1.5" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <x-form.input
                name="caption"
                :label="__('profile.add_youtube_caption_label')"
                wire:model="caption"
                :placeholder="__('profile.add_youtube_caption_placeholder')"
            />
            @error('caption')
                <p class="text-xs text-accent mt-1.5" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <x-parts.form-actions
            :submit-label="$isEdit ? __('profile.update_youtube_submit') : __('profile.add_youtube_submit')"
            :submitting-label="$isEdit ? __('profile.update_youtube_submitting') : __('profile.add_youtube_submitting')"
            :show-delete="$isEdit"
            :delete-label="__('profile.delete_youtube_submit')"
        />

    </form>
@endif
</div>
