<?php

use App\Enums\AnnouncementStatus;
use App\Enums\AnnouncementType;
use App\Models\Announcement;
use App\Models\Genre;
use App\Models\Instrument;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public ?string $model_id = null;

    #[Validate('required|min:5|max:100')]
    public string $title = '';

    #[Validate('required|in:musician_wanted,band_wanted,gig,lessons')]
    public string $type = '';

    #[Validate('required|exists:cities,id')]
    public ?int $city_id = null;

    #[Validate('required|min:20|max:1000')]
    public string $description = '';

    public array $selectedInstruments = [];
    public array $selectedGenres = [];

    public array $availableInstruments = [];
    public array $availableGenres = [];
    public array $availableTypes = [];

    public bool $success = false;

    public function mount(?string $model_id = null): void
    {
        $this->model_id = $model_id;
        $this->availableInstruments = Instrument::orderBy('name')->get(['id', 'name'])->toArray();
        $this->availableGenres = Genre::orderBy('name')->get(['id', 'name'])->toArray();
        $this->availableTypes = collect(AnnouncementType::cases())
            ->map(fn($case) => ['value' => $case->value, 'label' => __($case->label())])
            ->toArray();

        if ($model_id !== null) {
            $announcement = Announcement::findOrFail((int)$model_id);
            abort_unless(auth()->id() === $announcement->user_id, 403);

            $this->title = $announcement->title;
            $this->type = $announcement->type->value;
            $this->city_id = $announcement->city_id;
            $this->description = $announcement->description;
            $this->selectedInstruments = $announcement->instruments->pluck('id')->all();
            $this->selectedGenres = $announcement->genres->pluck('id')->all();
        }
    }

    public function save(): void
    {
        abort_unless(auth()->check(), 403);

        $this->validate();

        if ($this->model_id !== null) {
            $announcement = Announcement::findOrFail((int)$this->model_id);
            abort_unless(auth()->id() === $announcement->user_id, 403);

            $announcement->update([
                'city_id' => $this->city_id,
                'title' => $this->title,
                'description' => $this->description,
                'type' => $this->type,
            ]);

            $announcement->instruments()->sync($this->selectedInstruments);
            $announcement->genres()->sync($this->selectedGenres);

            $this->dispatch('announcement-updated', id: $announcement->id);
            $this->success = true;

            return;
        }

        $announcement = Announcement::create([
            'user_id' => auth()->id(),
            'city_id' => $this->city_id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'status' => AnnouncementStatus::Open,
        ]);

        $announcement->instruments()->sync($this->selectedInstruments);
        $announcement->genres()->sync($this->selectedGenres);

        $this->redirectRoute('announcement', ['id' => $announcement->id], navigate: true);
    }

    public function delete(): void
    {
        abort_unless($this->model_id !== null, 404);

        $announcement = Announcement::findOrFail((int)$this->model_id);
        abort_unless(auth()->id() === $announcement->user_id, 403);

        $announcement->delete();

        $this->dispatch('announcement-deleted', id: (int)$this->model_id);
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
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7 text-accent"
                     aria-hidden="true">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <div>
                <h3 class="font-heading text-xl text-heading">
                    {{ $model_id ? __('announcement.form_success_updated_title') : __('announcement.form_success_title') }}
                </h3>
                <p class="text-sm text-subtle mt-1">
                    {{ $model_id ? __('announcement.form_success_updated_body') : __('announcement.form_success_body') }}
                </p>
            </div>
            <button
                wire:click="close"
                type="button"
                class="h-11 px-6 rounded-md bg-dark text-on-dark text-sm font-medium hover:opacity-80 transition-opacity duration-150 cursor-pointer"
            >
                {{ __('announcement.form_close') }}
            </button>
        </div>
    @else
        <form wire:submit="save" class="space-y-5" novalidate>

            {{-- Titre --}}
            <div>
                <x-form.input
                    name="title"
                    :label="__('announcement.form_title_label')"
                    wire:model.live.blur="title"
                    :placeholder="__('announcement.form_title_placeholder')"
                    required
                />
                @error('title')
                <p class="text-xs text-accent mt-1.5" role="alert">{{ $message }}</p>
                @enderror
            </div>

            {{-- Type --}}
            <div>
                <x-form.select name="type" :label="__('announcement.form_type_label')" wire:model.live.blur="type"
                               required>
                    <option disabled selected value="">{{ __('announcement.form_type_placeholder') }}</option>
                    @foreach ($availableTypes as $typeOption)
                        <option value="{{ $typeOption['value'] }}">{{ $typeOption['label'] }}</option>
                    @endforeach
                </x-form.select>
                @error('type')
                <p class="text-xs text-accent mt-1.5" role="alert">{{ $message }}</p>
                @enderror
            </div>

            {{-- Ville --}}
            <div>
                <livewire:parts.form.locality-picker wire:model.live="city_id"/>
                @error('city_id')
                <p class="text-xs text-accent mt-1.5" role="alert">{{ $message }}</p>
                @enderror
            </div>

            {{-- Instruments --}}
            <fieldset>
                <legend
                    class="block text-sm font-medium text-subtle mb-1.5">{{ __('announcement.form_instruments_label') }}</legend>
                <div class="grid grid-cols-2 gap-x-4 gap-y-2">
                    @foreach ($availableInstruments as $instr)
                        <x-form.checkbox :name="'announcement_form_instrument_' . $instr['id']" wire:model="selectedInstruments"
                                         value="{{ $instr['id'] }}">
                            {{ $instr['name'] }}
                        </x-form.checkbox>
                    @endforeach
                </div>
            </fieldset>

            {{-- Genres --}}
            <fieldset>
                <legend
                    class="block text-sm font-medium text-subtle mb-1.5">{{ __('announcement.form_genres_label') }}</legend>
                <div class="grid grid-cols-2 gap-x-4 gap-y-2">
                    @foreach ($availableGenres as $genre)
                        <x-form.checkbox :name="'announcement_form_genre_' . $genre['id']" wire:model="selectedGenres"
                                         value="{{ $genre['id'] }}">
                            {{ $genre['name'] }}
                        </x-form.checkbox>
                    @endforeach
                </div>
            </fieldset>

            {{-- Description --}}
            <div>
                <x-form.textarea
                    name="description"
                    :label="__('announcement.form_description_label')"
                    wire:model.live.blur="description"
                    :placeholder="__('announcement.form_description_placeholder')"
                    :rows="4"
                    required
                />
                @error('description')
                <p class="text-xs text-accent mt-1.5" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <x-parts.form-actions
                :submit-label="$model_id ? __('announcement.form_update') : __('announcement.form_submit')"
                :submitting-label="$model_id ? __('announcement.form_updating') : __('announcement.form_submitting')"
                :show-delete="$model_id !== null"
                :delete-label="__('announcement.form_delete')"
            />

        </form>
    @endif
</div>
