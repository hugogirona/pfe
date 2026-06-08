<?php

namespace App\Support;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class LocalizedUrl
{
    /**
     * Localized URL of the current request for the given locale.
     *
     * The explore page carries a translated tab slug ("annonces" / "profils"),
     * so a naive locale swap would keep the French slug and 404. Here the slug
     * is translated into the target locale, keeping canonical, hreflang
     * alternates and the language switcher consistent and reachable.
     */
    public static function for(string $locale): string
    {
        if (request()->routeIs('explore') && filled(request()->route('tab'))) {
            $key = request()->route('tab') === __('explore.tab_announcements_slug')
                ? 'explore.tab_announcements_slug'
                : 'explore.tab_profiles_slug';

            return LaravelLocalization::getURLFromRouteNameTranslated($locale, 'routes.explore', ['tab' => __($key, [], $locale)]);
        }

        return LaravelLocalization::getLocalizedURL($locale);
    }
}
