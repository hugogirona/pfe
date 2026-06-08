@props(['variant' => 'dark'])

@php
    $active   = $variant === 'light' ? 'text-accent' : 'text-on-dark';
    $inactive = $variant === 'light' ? 'text-caption hover:text-subtle' : 'text-on-dark-caption hover:text-on-dark-subtle';
    $sep      = $variant === 'light' ? 'text-caption' : 'text-on-dark-caption';
    $locales = [
        'fr' => 'Passer en français',
        'en' => 'Switch to English',
        'nl' => 'Overschakelen naar Nederlands',
    ];
    $localizedUrl = function (string $locale) {
        if (request()->routeIs('explore') && filled(request()->route('tab'))) {
            $key = request()->route('tab') === __('explore.tab_announcements_slug')
                ? 'explore.tab_announcements_slug'
                : 'explore.tab_profiles_slug';

            return LaravelLocalization::getURLFromRouteNameTranslated($locale, 'routes.explore', ['tab' => __($key, [], $locale)]);
        }

        return LaravelLocalization::getLocalizedURL($locale);
    };
@endphp

<div class="flex items-center gap-3">
    @foreach($locales as $locale => $ariaLabel)
        <a href="{{ $localizedUrl($locale) }}"
           class="{{ app()->getLocale() === $locale ? $active : $inactive }} text-sm font-medium tracking-widest uppercase transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent rounded-sm"
           hreflang="{{ $locale }}"
           @if(app()->getLocale() === $locale) aria-current="true" @endif>
            {{ strtoupper($locale) }}<span class="sr-only"> — {{ $ariaLabel }}</span>
        </a>

        @if(! $loop->last)
            <span class="{{ $sep }} text-xs select-none" aria-hidden="true">|</span>
        @endif
    @endforeach
</div>
