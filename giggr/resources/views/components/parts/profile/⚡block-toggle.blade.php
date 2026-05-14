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
        <button
            type="button"
            wire:click="toggle"
            aria-label="{{ __('profile.unblock_name', ['name' => $name]) }}"
            class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium text-danger/70 hover:text-danger hover:bg-danger/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-danger"
        >
            <x-icon name="no-symbol" class="w-3.5 h-3.5"/>
            <span>{{ __('profile.unblock') }}</span>
        </button>
    @else
        <button
            type="button"
            x-show="!confirming"
            @click="confirming = true"
            aria-label="{{ __('profile.block_name', ['name' => $name]) }}"
            class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium text-danger/70 hover:text-danger hover:bg-danger/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-danger"
        >
            <x-icon name="no-symbol" class="w-3.5 h-3.5"/>
            <span>{{ __('profile.block') }}</span>
        </button>

        <div
            x-show="confirming"
            x-cloak
            x-transition.opacity.duration.150ms
            role="alertdialog"
            aria-live="polite"
            class="rounded-md border border-danger/15 bg-danger/5 px-3 py-2.5 space-y-2"
        >
            <p class="text-xs text-dark/80 leading-relaxed">{{ __('profile.block_confirm', ['name' => $name]) }}</p>
            <div class="flex items-center justify-end gap-1.5">
                <button
                    type="button"
                    @click="confirming = false"
                    class="px-2.5 py-1 rounded text-xs font-medium text-dark/60 hover:text-dark hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
                >
                    {{ __('profile.cancel') }}
                </button>
                <button
                    type="button"
                    wire:click="toggle"
                    @click="confirming = false"
                    class="px-2.5 py-1 rounded text-xs font-medium bg-danger text-bg hover:opacity-90 transition-opacity duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-danger focus-visible:ring-offset-1"
                >
                    {{ __('profile.block') }}
                </button>
            </div>
        </div>
    @endif
</div>
