<div class="hidden md:flex items-center gap-3">
    @auth
        {{-- Messaging --}}
        <button
            type="button"
            class="text-dark/50 hover:text-accent transition-colors duration-150 p-2 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-[6px] flex items-center justify-center"
            aria-label="{{ __('nav.aria_messaging') }}"
        >
            <x-icon name="chat-bubble" class="w-8 h-8"/>
        </button>

        {{-- Profile dropdown --}}
        <div
            class="relative"
            x-data="{ open: false, thumbnail: {{ auth()->user()->profile?->thumbnail ? json_encode(auth()->user()->profile->thumbnail) : 'null' }} }"
            @avatar-saved.window="thumbnail = $event.detail.thumbnail"
        >
            <button
                type="button"
                @click="open = !open"
                @keydown.escape.window="open = false"
                :aria-expanded="open"
                aria-haspopup="true"
                aria-label="{{ __('nav.aria_user_menu') }}"
                class="w-8 h-8 rounded-full overflow-hidden bg-dark/60 text-bg flex items-center justify-center text-sm font-semibold uppercase cursor-pointer hover:opacity-90 transition-opacity duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-2"
            >
                <img
                    x-show="thumbnail"
                    :src="thumbnail"
                    alt="{{ __('profile.avatar_alt', ['name' => auth()->user()->full_name]) }}"
                    class="w-full h-full object-cover object-center"
                />
                <span x-show="!thumbnail">{{ mb_substr(auth()->user()->full_name, 0, 1) }}</span>
            </button>

            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                @click.outside="open = false"
                class="absolute right-0 mt-2 w-44 bg-white rounded-xl border border-dark/10 shadow-lg overflow-hidden z-50"
                role="menu"
                style="display: none"
            >
                <a
                    href="{{ route('profile', ['id' => auth()->user()->id]) }}"
                    wire:navigate
                    @click="open = false"
                    role="menuitem"
                    class="flex items-center gap-2.5 px-4 py-3 text-sm text-dark hover:bg-dark/5 transition-colors duration-150 focus-visible:outline-none focus-visible:bg-dark/5"
                >
                    <x-icon name="user" class="w-4 h-4 text-dark/40"/>
                    {{ __('nav.view_profile') }}
                </a>

                <div class="border-t border-dark/8"></div>

                <form method="POST" action="/logout">
                    @csrf
                    <button
                        type="submit"
                        role="menuitem"
                        class="w-full flex items-center gap-2.5 px-4 py-3 text-sm text-danger/60 hover:text-danger hover:bg-danger/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:bg-danger/5"
                    >
                        <x-icon name="arrow-right-on-rectangle" class="w-4 h-4 text-danger/40"/>
                        {{ __('nav.sign_out') }}
                    </button>
                </form>
            </div>
        </div>
    @else
        <x-cta href="{{ route('login') }}" wire:navigate variant="dark">{{ __('nav.sign_in') }}</x-cta>
    @endauth
</div>
