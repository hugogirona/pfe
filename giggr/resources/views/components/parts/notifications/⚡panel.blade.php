<?php

use App\Models\Profile;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public const int PER_PAGE = 20;

    public int $perPage = self::PER_PAGE;

    public function mount(): void
    {
        abort_unless(auth()->check(), 403);
    }

    #[Computed]
    public function rows(): Collection
    {
        return auth()->user()
            ->notifications()
            ->latest()
            ->limit($this->perPage)
            ->get();
    }

    /**
     * Current avatar URL for each follower, keyed by profile id and resolved
     * live so a follower's later photo change is always reflected.
     *
     * @return array<int, string|null>
     */
    #[Computed]
    public function thumbnails(): array
    {
        $profileIds = $this->rows
            ->pluck('data.follower_profile_id')
            ->filter()
            ->unique()
            ->all();

        if ($profileIds === []) {
            return [];
        }

        return Profile::whereIn('id', $profileIds)
            ->get(['id', 'avatar_path'])
            ->mapWithKeys(fn (Profile $profile) => [$profile->id => $profile->thumbnail])
            ->all();
    }

    #[Computed]
    public function hasMore(): bool
    {
        return auth()->user()->notifications()->count() > $this->perPage;
    }

    #[Computed]
    public function hasUnread(): bool
    {
        return auth()->user()->unreadNotifications()->exists();
    }

    public function loadMore(): void
    {
        $this->perPage += self::PER_PAGE;

        unset($this->rows, $this->hasMore, $this->thumbnails);
    }

    public function markAllRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();

        unset($this->rows, $this->hasUnread);
        $this->dispatch('notifications-updated');
    }

    public function open(string $id): void
    {
        $notification = auth()->user()->notifications()->whereKey($id)->first();
        if ($notification === null) {
            return;
        }

        $notification->markAsRead();
        $this->dispatch('notifications-updated');

        $profileId = $notification->data['follower_profile_id'] ?? null;
        if ($profileId !== null) {
            $this->redirect(route('profile', ['id' => $profileId]), navigate: true);
        }
    }
};
?>

<div class="flex flex-col h-full">
    @if ($this->hasUnread)
        <div class="flex justify-end pb-3 mb-1 border-b border-dark/8">
            <x-cta variant="simple" size="sm" type="button" wire:click="markAllRead">
                {{ __('notifications.mark_all_read') }}
            </x-cta>
        </div>
    @endif

    @if ($this->rows->isEmpty())
        <div class="flex flex-col items-center justify-center flex-1 py-16 text-center">
            <div class="w-14 h-14 rounded-full bg-dark/5 flex items-center justify-center mb-3" aria-hidden="true">
                <x-icon name="bell" class="w-7 h-7 text-caption"/>
            </div>
            <p class="text-sm text-caption italic">{{ __('notifications.empty') }}</p>
        </div>
    @else
        <ul class="divide-y divide-dark/8 -mx-6">
            @foreach ($this->rows as $row)
                @php
                    $profileId = $row->data['follower_profile_id'] ?? null;
                    $thumbnail = $profileId ? ($this->thumbnails[$profileId] ?? null) : null;
                    $name = $row->data['follower_name'] ?? '';
                    $isUnread = $row->read_at === null;
                @endphp
                <li wire:key="notification-{{ $row->id }}">
                    <button
                        type="button"
                        wire:click="open('{{ $row->id }}')"
                        @class([
                            'w-full flex items-center gap-3 px-6 py-3.5 text-left transition-colors duration-150 cursor-pointer hover:bg-dark/[0.03] focus-visible:outline-none focus-visible:bg-dark/[0.03]',
                            'bg-pastel-salmon/30' => $isUnread,
                        ])
                    >
                        <div class="w-11 h-11 rounded-full overflow-hidden bg-pastel-taupe text-body flex items-center justify-center text-base font-semibold uppercase shrink-0" aria-hidden="true">
                            @if ($thumbnail)
                                <img src="{{ $thumbnail }}" alt="" class="w-full h-full object-cover"/>
                            @else
                                <span>{{ mb_substr($name, 0, 1) }}</span>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-body leading-snug">
                                {!! __('notifications.new_follower', ['name' => '<span class="font-semibold">'.e($name).'</span>']) !!}
                            </p>
                            <p class="text-xs text-caption mt-0.5">{{ $row->created_at->diffForHumans() }}</p>
                        </div>

                        @if ($isUnread)
                            <span class="w-2 h-2 rounded-full bg-accent shrink-0" aria-hidden="true"></span>
                        @endif
                    </button>
                </li>
            @endforeach
        </ul>

        @if ($this->hasMore)
            <div class="flex justify-center py-4">
                <x-cta variant="outline" size="sm" type="button" wire:click="loadMore" wire:loading.attr="disabled">
                    {{ __('notifications.load_more') }}
                </x-cta>
            </div>
        @endif
    @endif
</div>
