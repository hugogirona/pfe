<div x-data="{ lang: localStorage.getItem('lang') ?? 'fr' }"
     x-init="localStorage.setItem('lang', lang)"
     class="flex items-center gap-3">

    <button @click="lang = 'fr'; localStorage.setItem('lang', 'fr')"
            :class="lang === 'fr' ? 'text-bg' : 'text-bg/35 hover:text-bg/65'"
            class="text-xs font-medium tracking-widest uppercase transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-sm">
        FR
    </button>

    <span class="text-bg/20 text-xs select-none">|</span>

    <button @click="lang = 'en'; localStorage.setItem('lang', 'en')"
            :class="lang === 'en' ? 'text-bg' : 'text-bg/35 hover:text-bg/65'"
            class="text-xs font-medium tracking-widest uppercase transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-sm">
        EN
    </button>

</div>
