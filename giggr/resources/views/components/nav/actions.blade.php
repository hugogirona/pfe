<div class="hidden md:flex items-center gap-3">
    @auth
        {{-- Profile dropdown --}}
        <div
            class="relative"
            data-js-only
            x-data="{ open: false, thumbnail: {{ auth()->user()->profile?->thumbnail ? json_encode(auth()->user()->profile->thumbnail) : 'null' }} }"
            x-init="
                if (window.Echo) {
                    window.Echo.private('App.Models.User.{{ auth()->id() }}')
                        .listen('.avatar.processed', e => thumbnail = e.thumbnail);
                }
            "
        >
            <button
                type="button"
                @click="open = !open"
                @keydown.escape.window="open = false"
                :aria-expanded="open"
                aria-haspopup="true"
                aria-label="{{ __('nav.aria_user_menu') }}"
                class="w-10 h-10 flex items-center justify-center rounded-full cursor-pointer hover:opacity-90 transition-opacity duration-150 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
            >
                <span class="w-8 h-8 rounded-full overflow-hidden bg-dark/60 text-on-dark flex items-center justify-center text-sm font-semibold uppercase">
                    <img
                        x-show="thumbnail"
                        :src="thumbnail"
                        alt="{{ __('profile.avatar_alt', ['name' => auth()->user()->full_name]) }}"
                        class="w-full h-full object-cover object-center"
                    />
                    <span x-show="!thumbnail">{{ mb_substr(auth()->user()->full_name, 0, 1) }}</span>
                </span>
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
                @if (auth()->user()->profile)
                    <a
                        href="{{ route('profile', auth()->user()->profile) }}"
                        wire:navigate
                        @click="open = false"
                        role="menuitem"
                        class="flex items-center gap-2.5 px-4 py-3 text-sm text-body hover:bg-dark/5 transition-colors duration-150 focus-visible:outline-none focus-visible:bg-dark/5"
                    >
                        <x-icon name="user" class="w-4 h-4 text-caption"/>
                        {{ __('nav.view_profile') }}
                    </a>
                @endif

                <a
                    href="{{ route('settings.account') }}"
                    wire:navigate
                    @click="open = false"
                    role="menuitem"
                    class="flex items-center gap-2.5 px-4 py-3 text-sm text-body hover:bg-dark/5 transition-colors duration-150 focus-visible:outline-none focus-visible:bg-dark/5"
                >
                    <x-icon name="cog-6-tooth" class="w-4 h-4 text-caption"/>
                    {{ __('nav.settings') }}
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
        <x-cta href="{{ route('login') }}" wire:navigate variant="outline">{{ __('nav.sign_in') }}</x-cta>
    @endauth
</div>
