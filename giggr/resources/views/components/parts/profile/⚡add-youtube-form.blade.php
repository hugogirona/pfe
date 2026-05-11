<?php

use App\Enums\MediaType;
use App\Models\Media;
use App\Models\Profile;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    private const int MAX_MEDIAS_PER_PROFILE = 20;

    public ?string $model_id = null;

    #[Validate('required|string|regex:/^[A-Za-z0-9_-]{11}$/')]
    public string $videoId = '';

    #[Validate('nullable|string|max:255')]
    public string $caption = '';

    public function mount(?string $model_id = null): void
    {
        $this->model_id = $model_id;
    }

    public function save(): void
    {
        abort_unless(auth()->check(), 403);

        $profile = Profile::findOrFail((int) $this->model_id);
        abort_unless(auth()->id() === $profile->user_id, 403);

        $this->validate();

        if ($profile->media()->count() >= self::MAX_MEDIAS_PER_PROFILE) {
            $this->addError('videoId', __('profile.media_cap_reached', ['max' => self::MAX_MEDIAS_PER_PROFILE]));

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
            'caption' => $this->caption !== '' ? $this->caption : null,
            'position' => ($profile->media()->max('position') ?? -1) + 1,
        ]);

        $this->reset(['videoId', 'caption']);
        $this->dispatch('media-added');
        $this->dispatch('close-modal');
    }
};
?>

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

    <div class="flex justify-end pt-2 border-t border-dark/10">
        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-60 cursor-not-allowed"
            class="h-11 px-6 rounded-md bg-dark text-bg text-sm font-medium hover:opacity-80 transition-opacity duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
        >
            <span wire:loading.remove wire:target="save">{{ __('profile.add_youtube_submit') }}</span>
            <span wire:loading wire:target="save">{{ __('profile.add_youtube_submitting') }}</span>
        </button>
    </div>

</form>
