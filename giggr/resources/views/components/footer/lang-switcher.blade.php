@props(['variant' => 'dark'])

@php
    $active   = $variant === 'light' ? 'text-dark' : 'text-bg';
    $inactive = $variant === 'light' ? 'text-dark/30 hover:text-dark/70' : 'text-bg/35 hover:text-bg/65';
    $sep      = $variant === 'light' ? 'text-dark/20' : 'text-bg/20';
    $locales = [
        'fr' => 'Passer en français',
        'en' => 'Switch to English'
    ];
@endphp

<div class="flex items-center gap-3">
    @foreach($locales as $locale => $ariaLabel)
        <a href="{{ LaravelLocalization::getLocalizedURL($locale) }}"
           class="{{ app()->getLocale() === $locale ? $active : $inactive }} text-sm font-medium tracking-widest uppercase transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-sm"
           hreflang="{{ $locale }}"
           aria-label="{{ $ariaLabel }}"
           @if(app()->getLocale() === $locale) aria-current="true" @endif>
            {{ strtoupper($locale) }}
        </a>

        @if(! $loop->last)
            <span class="{{ $sep }} text-xs select-none" aria-hidden="true">|</span>
        @endif
    @endforeach
</div>
