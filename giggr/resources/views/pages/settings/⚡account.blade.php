<?php

use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app')] class extends Component {
    /** @var array<int, int> */
    public array $initialUserIds = [];

    /** @var array<int, bool> */
    public array $blockedState = [];

    public function mount(): void
    {
        abort_unless(auth()->check(), 403);

        $this->initialUserIds = auth()->user()
            ->blockedUsers()
            ->pluck('users.id')
            ->all();

        foreach ($this->initialUserIds as $id) {
            $this->blockedState[$id] = true;
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return $this->view()->title(__('titles.settings.account'));
    }

    #[Computed]
    public function rows(): Collection
    {
        if ($this->initialUserIds === []) {
            return collect();
        }

        return User::query()
            ->whereIn('id', $this->initialUserIds)
            ->with('profile')
            ->orderBy('first_name')
            ->get();
    }

    public function toggleBlock(int $userId): void
    {
        abort_unless(in_array($userId, $this->initialUserIds, true), 403);

        $target = User::find($userId);
        if ($target === null) {
            return;
        }

        if ($this->blockedState[$userId] ?? false) {
            auth()->user()->unblock($target);
            $this->blockedState[$userId] = false;
        } else {
            auth()->user()->block($target);
            $this->blockedState[$userId] = true;
        }
    }
};
?>

<div>
    <div class="max-w-3xl mx-auto px-6 py-10 space-y-6">

        <header>
            <h1 class="font-heading text-3xl text-dark">{{ __('settings.title') }}</h1>
            <p class="text-sm text-dark/50 mt-1 uppercase tracking-wider">{{ __('settings.account_section') }}</p>
        </header>

        <livewire:parts.settings.update-email />
        <livewire:parts.settings.update-password />
        <livewire:parts.settings.personal-info />
        <livewire:parts.settings.privacy />

        <x-settings.section
            labelledby="blocked-users-heading"
            :title="__('settings.blocked_users_title')"
            :description="__('settings.blocked_users_description')"
        >
            @if ($this->rows->isEmpty())
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <div class="w-14 h-14 rounded-full bg-dark/5 flex items-center justify-center mb-3" aria-hidden="true">
                        <x-icon name="no-symbol" class="w-7 h-7 text-dark/30"/>
                    </div>
                    <p class="text-sm text-dark/45 italic">{{ __('settings.blocked_users_empty') }}</p>
                </div>
            @else
                <ul class="divide-y divide-dark/8 -mx-6 md:-mx-8">
                    @foreach ($this->rows as $user)
                        @php
                            $thumbnail = $user->profile?->thumbnail;
                            $isBlocked = $blockedState[$user->id] ?? false;
                        @endphp
                        <li
                            class="flex items-center gap-3 px-6 md:px-8 py-3.5"
                            wire:key="blocked-user-{{ $user->id }}"
                        >
                            <div class="w-11 h-11 rounded-full overflow-hidden bg-pastel-taupe text-dark flex items-center justify-center text-base font-semibold uppercase shrink-0" aria-hidden="true">
                                @if ($thumbnail)
                                    <img src="{{ $thumbnail }}" alt="" class="w-full h-full object-cover"/>
                                @else
                                    <span>{{ mb_substr($user->full_name, 0, 1) }}</span>
                                @endif
                            </div>
                            <p class="flex-1 text-sm font-medium text-dark truncate">{{ $user->full_name }}</p>

                            @if ($isBlocked)
                                <button
                                    type="button"
                                    wire:click="toggleBlock({{ $user->id }})"
                                    aria-label="{{ __('profile.unblock_name', ['name' => $user->full_name]) }}"
                                    class="px-3 py-1.5 rounded-md text-xs font-medium text-dark/60 hover:text-dark hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
                                >
                                    {{ __('settings.unblock') }}
                                </button>
                            @else
                                <button
                                    type="button"
                                    wire:click="toggleBlock({{ $user->id }})"
                                    aria-label="{{ __('profile.block_name', ['name' => $user->full_name]) }}"
                                    class="px-3 py-1.5 rounded-md text-xs font-medium text-danger/70 hover:text-danger hover:bg-danger/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
                                >
                                    {{ __('settings.block') }}
                                </button>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-settings.section>

    </div>
</div>
