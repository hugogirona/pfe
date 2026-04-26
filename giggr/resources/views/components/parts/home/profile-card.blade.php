@props([
    'name'  => '',
    'age'   => '',
    'city'  => '',
    'bio'   => '',
    'image' => '',
    'url'   => '#',
])

<article class="flex flex-col w-full h-full rounded-xl overflow-hidden border border-dark/10 bg-bg shadow-sm">

    <div class="relative h-56 shrink-0 bg-dark/5">
        @if ($image)
            <img
                src="{{ Vite::asset('resources/img/profiles/' . $image) }}"
                alt="Photo de {{ $name }}"
                class="absolute inset-0 w-full h-full object-cover object-center"
            />
        @endif

        <button
            type="button"
            class="absolute top-3 right-3 w-10 h-10 flex items-center justify-center rounded-full bg-white/75 backdrop-blur-sm text-dark/40 hover:text-accent hover:bg-white/90 transition-colors duration-200 cursor-pointer"
            aria-label="{{ __('home.profile_favorite', ['name' => $name]) }}"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
            </svg>
        </button>
    </div>

    <div class="flex-1 px-6 py-5 space-y-3">
        <div>
            <h3 class="font-heading text-xl font-bold text-dark">{{ $name }}</h3>
            <p class="text-sm text-dark/40 mt-0.5">{{ $age }} ans · {{ $city }}</p>
        </div>
        <p class="text-base text-dark/60 leading-relaxed">{{ $bio }}</p>
    </div>

    <a
        href="{{ $url }}"
        class="group relative overflow-hidden flex items-center min-h-[52px] px-6 border-t border-dark/10 text-base font-medium cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-accent"
    >
        <span
            class="absolute inset-0 -translate-x-full bg-accent motion-safe:transition-transform motion-safe:duration-300 motion-safe:ease-out group-hover:translate-x-0 group-focus-visible:translate-x-0"
            aria-hidden="true"
        ></span>
        <span class="relative flex items-center gap-2 text-dark group-hover:text-bg group-focus-visible:text-bg motion-safe:transition-colors motion-safe:duration-100">
            {{ __('home.profile_contact') }}
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
            </svg>
        </span>
    </a>

</article>
