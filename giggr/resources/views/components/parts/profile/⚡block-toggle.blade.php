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

<div>
    @php $name = optional(\App\Models\User::find($targetUserId))->full_name ?? ''; @endphp
    <button
        type="button"
        wire:click="toggle"
        @if (! $isBlocked) wire:confirm="{{ __('profile.block_confirm', ['name' => $name]) }}" @endif
        aria-label="{{ $isBlocked ? __('profile.unblock_name', ['name' => $name]) : __('profile.block_name', ['name' => $name]) }}"
        class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium text-danger/70 hover:text-danger hover:bg-danger/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-danger"
    >
        <x-icon name="no-symbol" class="w-3.5 h-3.5"/>
        <span>{{ $isBlocked ? __('profile.unblock') : __('profile.block') }}</span>
    </button>
</div>
