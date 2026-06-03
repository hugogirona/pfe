@props([
    'profile',
    'isOwner'             => false,
    'allInstruments'      => [],
    'selectedInstruments' => [],
])

@php
    $sectionClass = \Illuminate\Support\Arr::toCssClasses([
        'relative px-6 pt-5 pb-4 border-b border-dark/[0.07]',
        'group/section' => $isOwner,
    ]);
@endphp

<section
    x-data="{ editing: false, snapshot: @js($selectedInstruments) }"
    class="{{ $sectionClass }}"
>
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-[11px] font-semibold uppercase tracking-widest text-caption">
            {{ __('profile.instruments_label') }}
        </h2>
        @if ($isOwner)
            <x-cta
                variant="simple"
                size="icon-sm"
                x-show="!editing"
                @click="editing = true"
                aria-label="{{ __('profile.edit_instruments') }}"
            >
                <x-icon name="pencil-square" class="w-3.5 h-3.5" />
            </x-cta>
        @endif
    </div>

    @if ($isOwner)
        <div class="grid motion-safe:transition-[grid-template-rows] motion-safe:duration-200 motion-safe:ease-out"
             :class="editing ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'">
            <div class="overflow-hidden">
                <div class="space-y-3 pb-1">
                    <div class="grid grid-cols-2 gap-x-4 gap-y-2">
                        @foreach ($allInstruments as $id => $label)
                            <x-form.checkbox :name="'instrument_' . $id" wire:model="selectedInstruments" value="{{ $id }}">{{ $label }}</x-form.checkbox>
                        @endforeach
                    </div>
                    <x-parts.profile.inline-edit-actions>
                        <x-slot:cancel @click="$wire.set('selectedInstruments', snapshot); editing = false">
                            {{ __('profile.cancel') }}
                        </x-slot:cancel>
                        <x-slot:save wire:click="saveInstruments" @instruments-saved.window="snapshot = [...$wire.selectedInstruments]; editing = false">
                            {{ __('profile.save') }}
                        </x-slot:save>
                    </x-parts.profile.inline-edit-actions>
                </div>
            </div>
        </div>

        <div class="grid motion-safe:transition-[grid-template-rows] motion-safe:duration-200 motion-safe:ease-out"
             :class="!editing ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'">
            <div class="overflow-hidden">
                @if ($profile->instruments->isNotEmpty())
                    <ul class="flex flex-wrap gap-1.5">
                        @foreach ($profile->instruments as $instr)
                            <li><x-pill variant="instrument">{{ $instr->name }}</x-pill></li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-xs text-caption italic">{{ __('profile.instruments_empty_owner') }}</p>
                @endif
            </div>
        </div>
    @else
        @if ($profile->instruments->isNotEmpty())
            <ul class="flex flex-wrap gap-1.5">
                @foreach ($profile->instruments as $instr)
                    <li><x-pill variant="instrument">{{ $instr->name }}</x-pill></li>
                @endforeach
            </ul>
        @endif
    @endif
</section>
