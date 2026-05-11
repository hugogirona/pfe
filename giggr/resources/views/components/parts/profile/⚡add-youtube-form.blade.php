<?php

use App\Enums\MediaType;
use App\Models\Media;
use App\Models\Profile;
use App\Support\YouTubeUrl;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    private const int MAX_MEDIAS_PER_PROFILE = 20;

    public ?string $model_id = null;

    #[Validate('required|string|max:500')]
    public string $url = '';

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

        $id = YouTubeUrl::extractId($this->url);
        if ($id === null) {
            $this->addError('url', __('profile.media_invalid_youtube_url'));

            return;
        }

        if ($profile->media()->count() >= self::MAX_MEDIAS_PER_PROFILE) {
            $this->addError('url', __('profile.media_cap_reached', ['max' => self::MAX_MEDIAS_PER_PROFILE]));

            return;
        }

        $duplicate = $profile->media()
            ->where('type', MediaType::Youtube->value)
            ->where('source', $id)
            ->exists();
        if ($duplicate) {
            $this->addError('url', __('profile.media_youtube_duplicate'));

            return;
        }

        Media::create([
            'profile_id' => $profile->id,
            'type' => MediaType::Youtube,
            'source' => $id,
            'caption' => $this->caption !== '' ? $this->caption : null,
            'position' => ($profile->media()->max('position') ?? -1) + 1,
        ]);

        $this->reset(['url', 'caption']);
        $this->dispatch('media-added');
        $this->dispatch('close-modal');
    }
};
?>

<form wire:submit="save" class="space-y-5" novalidate>

    <div>
        <x-form.input
            name="url"
            :label="__('profile.add_youtube_label')"
            wire:model="url"
            :placeholder="__('profile.add_youtube_placeholder')"
            required
        />
        @error('url')
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
