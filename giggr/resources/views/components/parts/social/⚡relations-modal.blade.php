<?php

use App\Models\Follow;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    private const array VALID_TABS = ['followers', 'followed'];

    public bool $open = false;

    public ?int $profileId = null;

    public string $activeTab = 'followers';

    public string $musicianName = '';

    public bool $isOwnProfile = false;

    public array $followedIds = [];

    #[On('open-relations-modal')]
    public function openModal(int $profileId, string $tab = 'followers'): void
    {
        $this->profileId = $profileId;
        $this->activeTab = in_array($tab, self::VALID_TABS, true) ? $tab : 'followers';

        $profile = Profile::with('user')->find($profileId);
        $this->musicianName = $profile?->user->full_name ?? '';
        $this->isOwnProfile = auth()->check() && $profile !== null && auth()->id() === $profile->user_id;

        $this->open = true;
        $this->refreshFollowedIds();
    }

    public function setTab(string $tab): void
    {
        if (!in_array($tab, self::VALID_TABS, true)) {
            return;
        }

        $this->activeTab = $tab;
        $this->refreshFollowedIds();
    }

    public function toggleFollow(int $profileId): void
    {
        abort_unless(auth()->check(), 403);

        $viewer = auth()->user();
        $target = Profile::find($profileId);
        if ($target === null || $target->user_id === $viewer->id) {
            return;
        }

        if ($viewer->isFollowing($target)) {
            $viewer->unfollow($target);
            $this->followedIds = array_values(array_filter(
                $this->followedIds,
                fn(int $id) => $id !== $profileId,
            ));
        } else {
            $viewer->follow($target);
            $this->followedIds[] = $profileId;
        }

        $this->dispatch('follow-state-changed');
    }

    public function close(): void
    {
        $this->open = false;
    }

    private function refreshFollowedIds(): void
    {
        $viewer = auth()->user();
        if ($viewer === null || $this->profileId === null) {
            $this->followedIds = [];

            return;
        }

        $list = $this->activeTab === 'followers' ? $this->followers : $this->followed;
        $rowIds = $list->pluck('id')->all();
        if ($rowIds === []) {
            $this->followedIds = [];

            return;
        }

        $this->followedIds = Follow::query()
            ->where('user_id', $viewer->id)
            ->where('followable_type', 'profile')
            ->whereIn('followable_id', $rowIds)
            ->pluck('followable_id')
            ->all();
    }

    #[Computed]
    public function followers(): Collection
    {
        if ($this->profileId === null) {
            return new Collection;
        }

        return Profile::query()
            ->with(['user', 'city'])
            ->whereHas('user.follows', fn($q) => $q
                ->where('followable_type', 'profile')
                ->where('followable_id', $this->profileId)
            )
            ->orderBy('id')
            ->get();
    }

    #[Computed]
    public function followed(): Collection
    {
        if ($this->profileId === null) {
            return new Collection;
        }

        $profile = Profile::find($this->profileId);
        if ($profile === null) {
            return new Collection;
        }

        $ownerFollowsIds = Follow::query()
            ->where('user_id', $profile->user_id)
            ->where('followable_type', 'profile')
            ->pluck('followable_id');

        return Profile::query()
            ->with(['user', 'city'])
            ->whereIn('id', $ownerFollowsIds)
            ->orderBy('id')
            ->get();
    }
};
?>

<div
    x-data="{ show: $wire.entangle('open').live }"
    x-init="$watch('show', val => {
        if (val) {
            document.body.style.overflow = 'hidden';
        } else {
            setTimeout(() => document.body.style.overflow = '', 200);
        }
    })"
    x-show="show"
    @keydown.escape.window="if (show) $wire.close()"
    class="fixed inset-0 z-60 flex items-center justify-center p-4"
    style="display: none"
    role="dialog"
    aria-modal="true"
    aria-labelledby="relations-modal-title"
>
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="$wire.close()"
        class="fixed inset-0 bg-dark/55 backdrop-blur-sm"
        aria-hidden="true"
    ></div>

    <div
        x-show="show"
        x-transition:enter="transition ease-[cubic-bezier(0.34,1.56,0.64,1)] duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        class="relative z-10 w-full max-w-md max-h-[80vh] rounded-2xl bg-bg shadow-2xl flex flex-col overflow-hidden"
    >
        <header class="flex items-center justify-between gap-2 px-2 pr-3 pt-2 border-b border-dark/10 shrink-0">
            <div role="tablist" aria-label="{{ __('social.relations_aria') }}" class="flex flex-1">
                <button
                    type="button"
                    role="tab"
                    aria-selected="{{ $activeTab === 'followers' ? 'true' : 'false' }}"
                    aria-controls="relations-panel-followers"
                    wire:click="setTab('followers')"
                    @class([
                        'flex-1 px-4 py-3 text-sm font-medium transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-md',
                        'text-body border-b-2 border-accent -mb-px' => $activeTab === 'followers',
                        'text-caption hover:text-subtle border-b-2 border-transparent -mb-px' => $activeTab !== 'followers',
                    ])
                >
                    {{ __('social.tab_followers') }}
                    <span class="ml-1 text-caption">{{ $this->followers->count() }}</span>
                </button>
                <button
                    type="button"
                    role="tab"
                    aria-selected="{{ $activeTab === 'followed' ? 'true' : 'false' }}"
                    aria-controls="relations-panel-followed"
                    wire:click="setTab('followed')"
                    @class([
                        'flex-1 px-4 py-3 text-sm font-medium transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-md',
                        'text-body border-b-2 border-accent -mb-px' => $activeTab === 'followed',
                        'text-caption hover:text-subtle border-b-2 border-transparent -mb-px' => $activeTab !== 'followed',
                    ])
                >
                    {{ __('social.tab_followed') }}
                    <span class="ml-1 text-caption">{{ $this->followed->count() }}</span>
                </button>
            </div>

            <x-cta
                variant="simple"
                size="icon-round"
                class="shrink-0"
                type="button"
                wire:click="close"
                aria-label="{{ __('social.close_relations') }}"
            >
                <x-icon name="x-mark" class="w-5 h-5"/>
            </x-cta>
        </header>

        {{-- Panel --}}
        <h2 id="relations-modal-title" class="sr-only">
            {{ $activeTab === 'followers' ? __('social.tab_followers') : __('social.tab_followed') }}
            — {{ $musicianName }}
        </h2>

        @php
            $list = $activeTab === 'followers' ? $this->followers : $this->followed;
            $panelId = 'relations-panel-'.$activeTab;
        @endphp

        <div
            id="{{ $panelId }}"
            role="tabpanel"
            class="flex-1 overflow-y-auto"
        >
            @if ($list->isEmpty())
                @php
                    $emptyKey = match (true) {
                        $isOwnProfile && $activeTab === 'followers' => 'social.no_followers_own',
                        $isOwnProfile && $activeTab === 'followed' => 'social.no_followed_own',
                        $activeTab === 'followers' => 'social.no_followers',
                        default => 'social.no_followed',
                    };
                @endphp
                <div class="p-12 text-center">
                    <p class="text-sm text-subtle leading-relaxed max-w-xs mx-auto">
                        {{ __($emptyKey) }}
                    </p>
                </div>
            @else
                <ul class="divide-y divide-dark/[0.07]">
                    @foreach ($list as $row)
                        <li>
                            <x-parts.social.compact-musician-row
                                :profile="$row"
                                :show-follow-button="auth()->check()"
                                :is-following="in_array($row->id, $followedIds, true)"
                                :viewer-id="auth()->id()"
                            />
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
