<div class="flex items-center gap-3">

    <a href="{{ route('home') }}"
       class="{{ app()->getLocale() === 'fr' ? 'text-bg' : 'text-bg/35 hover:text-bg/65' }} text-base font-medium tracking-widest uppercase transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-sm">
        FR
    </a>

    <span class="text-bg/20 text-xs select-none">|</span>

    <a href="{{ route('en.home') }}"
       class="{{ app()->getLocale() === 'en' ? 'text-bg' : 'text-bg/35 hover:text-bg/65' }} text-base font-medium tracking-widest uppercase transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-sm">
        EN
    </a>

</div>
