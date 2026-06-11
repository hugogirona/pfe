<?php

use App\Models\Announcement;
use App\Models\Follow;
use App\Models\Profile;
use Livewire\Component;

new class extends Component {
    public string $type = 'profiles';

    public string $bg = 'bg-pastel-salmon';

    public $items;

    /** @var array<int, int> */
    public array $followedProfileIds = [];

    public function mount(string $type = 'profiles', string $bg = 'bg-pastel-salmon'): void
    {
        abort_unless(in_array($type, ['profiles', 'announcements'], true), 404);

        $this->type = $type;
        $this->bg = $bg;

        $this->items = $this->type === 'announcements'
            ? Announcement::with(['city', 'instruments', 'genres'])->active()->latest()->limit(7)->get()
            : Profile::with(['user', 'city', 'instruments', 'genres'])
                ->whereNotNull('avatar_path')
                ->whereNotNull('bio')
                ->inRandomOrder()
                ->limit(7)
                ->get();

        if ($this->type === 'profiles') {
            $viewer = auth()->user();
            if ($viewer !== null && $this->items->isNotEmpty()) {
                $this->followedProfileIds = Follow::query()
                    ->where('user_id', $viewer->id)
                    ->where('followable_type', 'profile')
                    ->whereIn('followable_id', $this->items->pluck('id'))
                    ->pluck('followable_id')
                    ->all();
            }
        }
    }
};
?>

@php
    $sliderId = 'slider-'.$type;
    $cardClass = 'snap-start shrink-0 flex flex-col min-w-[250px] w-[85%] sm:w-[calc(50%-12px)] md:w-[calc(33.333%-16px)]';
@endphp

<section class="py-12 md:py-24 {{ $bg }}">
    <div class="max-w-6xl mx-auto px-6">

        @if ($items->isNotEmpty())
            <div class="text-center mb-10">
                <h2 class="font-heading text-4xl md:text-5xl text-heading">{{ __('home.'.$type.'_title') }}</h2>
                <p class="mt-4 text-base md:text-lg text-subtle">{{ __('home.'.$type.'_subtitle') }}</p>
            </div>

            <div class="relative" x-data="homeSlider({{ $items->count() }})">
                <button
                    @click="prev()"
                    class="hidden md:flex absolute -left-5 top-[calc(50%-2rem)] -translate-y-1/2 z-10 w-11 h-11 items-center justify-center rounded-full bg-bg border border-dark/15 shadow-sm text-body hover:bg-dark hover:text-on-dark transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
                    aria-label="{{ __('home.carousel_prev') }}"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </button>

                <ul
                    x-ref="track"
                    @scroll.passive="onScroll()"
                    class="flex gap-6 overflow-x-auto snap-x snap-mandatory scroll-smooth scrollbar-none overscroll-x-contain py-2 list-none m-0 p-0 scroll-pl-[7.5%] sm:scroll-pl-0"
                >
                    @foreach ($items as $item)
                        <li id="{{ $sliderId }}-{{ $loop->index }}" @class([$cardClass, 'scroll-mt-24', 'ml-[7.5%] sm:ml-0' => $loop->first, 'mr-[7.5%] sm:mr-0' => $loop->last])>
                            @if ($type === 'announcements')
                                <x-parts.explore.announcement-card :announcement="$item" />
                            @else
                                <x-profile-card :profile="$item" :followed-profile-ids="$followedProfileIds" />
                            @endif
                        </li>
                    @endforeach
                </ul>

                <button
                    @click="next()"
                    class="hidden md:flex absolute -right-5 top-[calc(50%-2rem)] -translate-y-1/2 z-10 w-11 h-11 items-center justify-center rounded-full bg-bg border border-dark/15 shadow-sm text-body hover:bg-dark hover:text-on-dark transition-colors duration-200 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
                    aria-label="{{ __('home.carousel_next') }}"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </button>

                <div role="group" class="flex justify-center items-center mt-8" aria-label="{{ __('home.carousel_aria', ['section' => __('home.'.$type.'_title')]) }}">
                    <template x-for="i in pageCount" :key="i">
                        <a
                            :href="`#{{ $sliderId }}-${i - 1}`"
                            @click.prevent="go(i - 1)"
                            :aria-label="`{{ __('home.carousel_goto') }}${i}`"
                            :aria-current="current === i - 1 ? 'true' : false"
                            class="group flex items-center justify-center h-6 px-2 cursor-pointer rounded-full focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
                        >
                            <span
                                class="h-2 rounded-full transition-all duration-300 motion-reduce:transition-none"
                                :class="current === i - 1 ? 'w-6 bg-accent' : 'w-2 bg-dark/25 group-hover:bg-dark/45'"
                            ></span>
                        </a>
                    </template>
                </div>
            </div>
        @endif

    </div>
</section>
