<?php

use App\Models\Profile;
use Livewire\Component;

new class extends Component {
    public $profiles;

    public function mount(): void
    {
        $this->profiles = Profile::with(['user', 'city', 'instruments', 'genres'])
            ->inRandomOrder()
            ->limit(7)
            ->get();
    }
};
?>

@php
    $cardClass = 'snap-start shrink-0 flex flex-col min-w-[250px] w-[85%] sm:w-[calc(50%-12px)] md:w-[calc(33.333%-16px)]';
@endphp

<section class="py-16 md:py-20 bg-pastel-salmon">
    <div class="max-w-6xl mx-auto px-6">

        <div class="text-center mb-10">
            <h2 class="font-heading text-4xl md:text-5xl text-dark">{{ __('home.musicians_title') }}</h2>
            <p class="mt-4 text-base md:text-lg text-dark/55">{{ __('home.musicians_subtitle') }}</p>
        </div>

        <div
            class="relative"
            x-data="musiciansSlider({{ $profiles->count() }})"
        >
            <button
                @click="prev()"
                class="hidden md:flex absolute -left-5 top-[calc(50%-2rem)] -translate-y-1/2 z-10 w-11 h-11 items-center justify-center rounded-full bg-bg border border-dark/15 shadow-sm text-dark hover:bg-dark hover:text-bg transition-colors duration-200 cursor-pointer"
                aria-label="{{ __('home.carousel_prev') }}"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </button>

            <div
                x-ref="track"
                @scroll.passive="onScroll()"
                class="flex gap-6 overflow-x-auto snap-x snap-mandatory scroll-smooth scrollbar-none overscroll-x-contain pb-2"
            >
                @foreach ($profiles as $profile)
                    <div class="{{ $cardClass }}">
                        <x-musician-card :profile="$profile" />
                    </div>
                @endforeach
            </div>

            <button
                @click="next()"
                class="hidden md:flex absolute -right-5 top-[calc(50%-2rem)] -translate-y-1/2 z-10 w-11 h-11 items-center justify-center rounded-full bg-bg border border-dark/15 shadow-sm text-dark hover:bg-dark hover:text-bg transition-colors duration-200 cursor-pointer"
                aria-label="{{ __('home.carousel_next') }}"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </button>

            <div class="flex justify-center items-center gap-2 mt-8" role="tablist" aria-label="{{ __('home.carousel_aria') }}">
                <template x-for="i in pageCount" :key="i">
                    <button
                        @click="goTo(i - 1)"
                        :aria-label="`{{ __('home.carousel_goto') }}${i}`"
                        :aria-selected="current === i - 1"
                        class="h-2 rounded-full transition-all duration-300 cursor-pointer motion-reduce:transition-none"
                        :class="current === i - 1 ? 'w-6 bg-accent' : 'w-2 bg-dark/25 hover:bg-dark/45'"
                    ></button>
                </template>
            </div>

        </div>

    </div>
</section>