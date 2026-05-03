@props(['bio', 'isOwner' => false])

<div x-data="{ editing: false, snapshot: @js($bio ?? '') }">
    <div class="flex items-start justify-between gap-3 mb-4">
        <h2 id="about-heading" class="font-heading text-2xl text-dark">
            {{ __('profile.about_title') }}
        </h2>
        @if ($isOwner)
            <x-cta
                variant="simple"
                size="icon"
                x-show="!editing"
                @click="snapshot = $wire.bio; editing = true"
                class="shrink-0"
                aria-label="{{ __('profile.edit_bio') }}"
            >
                <x-icon name="pencil-square" class="w-4 h-4" />
            </x-cta>
        @endif
    </div>

    @if ($isOwner)
        {{-- Edit mode --}}
        <div class="grid motion-safe:transition-[grid-template-rows] motion-safe:duration-200 motion-safe:ease-out"
             :class="editing ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'">
            <div class="overflow-hidden">
                <div class="pb-1">
                    <x-form.textarea
                        name="bio"
                        :label="__('profile.about_title')"
                        :rows="5"
                        wire:model="bio"
                    />
                    @error('bio')
                        <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
                    @enderror
                    <div class="flex justify-end items-center gap-4 mt-4">
                        <x-cta
                            variant="dark"
                            size="xs"
                            wire:click="saveBio"
                            @bio-saved.window="editing = false"
                        >{{ __('profile.save') }}</x-cta>
                        <x-cta
                            variant="simple"
                            size="xs"
                            @click="$wire.set('bio', snapshot); editing = false"
                        >{{ __('profile.cancel') }}</x-cta>
                    </div>
                </div>
            </div>
        </div>

        {{-- View mode --}}
        <div class="grid motion-safe:transition-[grid-template-rows] motion-safe:duration-200 motion-safe:ease-out"
             :class="!editing ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'">
            <div class="overflow-hidden">
                @if ($bio)
                    <p class="text-dark/65 leading-relaxed text-[15px]">{{ $bio }}</p>
                @else
                    <p class="text-sm text-dark/40 italic">{{ __('profile.add_bio_empty') }}</p>
                @endif
            </div>
        </div>
    @else
        <p class="text-dark/65 leading-relaxed text-[15px]">{{ $bio }}</p>
    @endif

</div>
