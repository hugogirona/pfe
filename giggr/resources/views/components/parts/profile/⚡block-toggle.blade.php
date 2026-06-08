<?php

use App\Models\User;
use Livewire\Component;

new class extends Component {
    public int $targetUserId;

    public bool $isBlocked = false;

    public function mount(int $targetUserId): void
    {
        abort_unless(auth()->check(), 403);
        abort_unless(auth()->id() !== $targetUserId, 403);

        $this->targetUserId = $targetUserId;
        $this->isBlocked = auth()->user()->blockedUsers()->whereKey($targetUserId)->exists();
    }

    public function toggle(): void
    {
        $target = User::find($this->targetUserId);
        abort_unless($target !== null, 404);

        if ($this->isBlocked) {
            auth()->user()->unblock($target);
        } else {
            auth()->user()->block($target);
        }

        $this->isBlocked = ! $this->isBlocked;
    }
};
?>

<div x-data="{ confirming: false }">
    @php $name = optional(\App\Models\User::find($targetUserId))->full_name ?? ''; @endphp

    @if ($isBlocked)
        <x-cta
            variant="danger"
            size="sm"
            class="w-full gap-1.5"
            type="button"
            wire:click="toggle"
            aria-label="{{ __('profile.unblock_name', ['name' => $name]) }}"
        >
            <x-icon name="no-symbol" class="w-3.5 h-3.5"/>
            <span>{{ __('profile.unblock') }}</span>
        </x-cta>
    @else
        <x-cta
            variant="danger"
            size="sm"
            class="w-full gap-1.5"
            type="button"
            x-show="!confirming"
            @click="confirming = true"
            aria-label="{{ __('profile.block_name', ['name' => $name]) }}"
        >
            <x-icon name="no-symbol" class="w-3.5 h-3.5"/>
            <span>{{ __('profile.block') }}</span>
        </x-cta>

        <div
            x-show="confirming"
            x-cloak
            x-transition.opacity.duration.150ms
            role="alertdialog"
            aria-live="polite"
            class="rounded-md border border-danger/15 bg-danger/5 px-3 py-2.5 space-y-2"
        >
            <p class="text-xs text-subtle leading-relaxed">{{ __('profile.block_confirm', ['name' => $name]) }}</p>
            <div class="flex items-center justify-end gap-1.5">
                <x-cta
                    variant="simple"
                    size="sm"
                    type="button"
                    @click="confirming = false"
                >
                    {{ __('profile.cancel') }}
                </x-cta>
                <x-cta
                    variant="danger-solid"
                    size="sm"
                    type="button"
                    wire:click="toggle"
                    @click="confirming = false"
                >
                    {{ __('profile.block') }}
                </x-cta>
            </div>
        </div>
    @endif
</div>
