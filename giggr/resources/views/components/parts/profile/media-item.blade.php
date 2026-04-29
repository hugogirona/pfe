@props(['item', 'musician'])

<figure class="group">

    <div class="relative rounded-xl overflow-hidden bg-dark/5 aspect-[4/3] cursor-pointer">

        @if ($item['type'] === 'video')

            {{-- Video thumbnail --}}
            @if (!empty($item['thumbnail']))
                <img
                    src="{{ Vite::asset('resources/img/profiles/' . $item['thumbnail']) }}"
                    alt=""
                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-300 motion-safe:group-hover:scale-105"
                    loading="lazy"
                />
            @else
                <div class="absolute inset-0 bg-gradient-to-br from-dark/80 to-dark"></div>
            @endif

            {{-- Dark overlay on hover --}}
            <div class="absolute inset-0 bg-dark/20 group-hover:bg-dark/40 transition-colors duration-200"></div>

            <button
                type="button"
                class="absolute inset-0 flex items-center justify-center focus-visible:outline-none cursor-pointer focus-visible:ring-2 focus-visible:ring-bg focus-visible:ring-inset"
                aria-label="{{ __('profile.video_play') }}"
            >
                <span class="w-14 h-14 rounded-full bg-bg/90 backdrop-blur-sm flex items-center justify-center shadow-lg
                             group-hover:bg-bg group-hover:scale-110 transition-all duration-200 motion-safe:transition-transform">
                    <x-icon name="play" class="w-5 h-5 text-dark ml-0.5" />
                </span>
            </button>

        @else

            {{-- Photo --}}
            @if (!empty($item['src']))
                <img
                    src="{{ Vite::asset('resources/img/profiles/' . $item['src']) }}"
                    alt="{{ __('profile.photo_alt', ['name' => $musician['name']]) }}"
                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-300 motion-safe:group-hover:scale-105"
                    loading="lazy"
                />
            @else
                <div class="absolute inset-0 flex items-center justify-center bg-pastel-taupe">
                    <x-icon name="music-note" class="w-10 h-10 text-dark/20" />
                </div>
            @endif

            {{-- Hover overlay --}}
            <div class="absolute inset-0 bg-dark/0 group-hover:bg-dark/15 transition-colors duration-200"></div>

        @endif

    </div>


</figure>
