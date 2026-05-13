<?php

use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Paramètres — Giggr.')] class extends Component {
    public function mount(): void
    {
        abort_unless(auth()->check(), 403);
    }

    #[Computed]
    public function blockedUsers(): Collection
    {
        return auth()->user()
            ->blockedUsers()
            ->with('profile')
            ->orderBy('users.first_name')
            ->get();
    }

    public function unblock(int $userId): void
    {
        abort_unless(auth()->check(), 403);

        $target = User::find($userId);
        if ($target === null) {
            return;
        }

        auth()->user()->unblock($target);

        unset($this->blockedUsers);
    }
};
?>

<div>
    <div class="max-w-3xl mx-auto px-6 py-10">

        <header class="mb-8">
            <h1 class="font-heading text-3xl text-dark">{{ __('settings.title') }}</h1>
            <p class="text-sm text-dark/50 mt-1 uppercase tracking-wider">{{ __('settings.account_section') }}</p>
        </header>

        <section
            aria-labelledby="blocked-users-heading"
            class="bg-white rounded-2xl border border-dark/10 shadow-sm p-6 md:p-8"
        >
            <header class="mb-5">
                <h2 id="blocked-users-heading" class="font-heading text-xl text-dark">
                    {{ __('settings.blocked_users_title') }}
                </h2>
                <p class="text-sm text-dark/55 mt-1.5 leading-relaxed">
                    {{ __('settings.blocked_users_description') }}
                </p>
            </header>

            @if ($this->blockedUsers->isEmpty())
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <div class="w-14 h-14 rounded-full bg-dark/5 flex items-center justify-center mb-3" aria-hidden="true">
                        <x-icon name="no-symbol" class="w-7 h-7 text-dark/30"/>
                    </div>
                    <p class="text-sm text-dark/45 italic">{{ __('settings.blocked_users_empty') }}</p>
                </div>
            @else
                <ul class="divide-y divide-dark/8 -mx-6 md:-mx-8">
                    @foreach ($this->blockedUsers as $user)
                        @php $thumbnail = $user->profile?->thumbnail; @endphp
                        <li class="flex items-center gap-3 px-6 md:px-8 py-3.5">
                            <div class="w-11 h-11 rounded-full overflow-hidden bg-pastel-taupe text-dark flex items-center justify-center text-base font-semibold uppercase shrink-0" aria-hidden="true">
                                @if ($thumbnail)
                                    <img src="{{ $thumbnail }}" alt="" class="w-full h-full object-cover"/>
                                @else
                                    <span>{{ mb_substr($user->full_name, 0, 1) }}</span>
                                @endif
                            </div>
                            <p class="flex-1 text-sm font-medium text-dark truncate">{{ $user->full_name }}</p>
                            <button
                                type="button"
                                wire:click="unblock({{ $user->id }})"
                                wire:confirm="{{ __('settings.unblock_confirm') }}"
                                class="px-3 py-1.5 rounded-md text-xs font-medium text-dark/60 hover:text-dark hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
                            >
                                {{ __('settings.unblock') }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

    </div>
</div>
