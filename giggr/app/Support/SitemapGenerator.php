<?php

namespace App\Support;

use Mcamara\LaravelLocalization\Exceptions\SupportedLocalesNotDefined;
use Mcamara\LaravelLocalization\Exceptions\UnsupportedLocaleException;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class SitemapGenerator
{
    /**
     * Build the XML sitemap for every public, indexable page in each locale.
     *
     * Authenticated areas (profiles, announcements, settings, auth flow) are
     * intentionally excluded: they redirect guests to the login page and must
     * not be crawled.
     */
    public function toXml(): string
    {
        $default = LaravelLocalization::getDefaultLocale();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n";

        foreach ($this->pages() as $page) {
            $alternates = $this->localizedUrls($page['url']);
            $xDefault = $alternates[$default] ?? reset($alternates);

            foreach ($alternates as $loc) {
                $xml .= $this->renderUrl($loc, $alternates, $xDefault, $page['changefreq'], $page['priority']);
            }
        }

        return $xml.'</urlset>'."\n";
    }

    private function pages(): array
    {
        return [
            ['changefreq' => 'weekly', 'priority' => '1.0', 'url' => fn (string $locale) => LaravelLocalization::getLocalizedURL($locale, url('/'))],
            ['changefreq' => 'daily', 'priority' => '0.9', 'url' => fn (string $locale) => $this->routeUrl($locale, 'routes.explore')],
            ['changefreq' => 'daily', 'priority' => '0.8', 'url' => fn (string $locale) => $this->routeUrl($locale, 'routes.explore', ['tab' => __('explore.tab_profiles_slug', [], $locale)])],
            ['changefreq' => 'daily', 'priority' => '0.8', 'url' => fn (string $locale) => $this->routeUrl($locale, 'routes.explore', ['tab' => __('explore.tab_announcements_slug', [], $locale)])],
            ['changefreq' => 'monthly', 'priority' => '0.6', 'url' => fn (string $locale) => $this->routeUrl($locale, 'routes.register')],
            ['changefreq' => 'monthly', 'priority' => '0.5', 'url' => fn (string $locale) => $this->routeUrl($locale, 'routes.contact')],
            ['changefreq' => 'yearly', 'priority' => '0.3', 'url' => fn (string $locale) => $this->routeUrl($locale, 'routes.privacy')],
        ];
    }

    /**
     * @throws UnsupportedLocaleException
     * @throws SupportedLocalesNotDefined
     */
    private function routeUrl(string $locale, string $routeKey, array $attributes = []): string
    {
        return LaravelLocalization::getURLFromRouteNameTranslated($locale, $routeKey, $attributes);
    }

    private function localizedUrls(callable $resolver): array
    {
        $urls = [];

        foreach (LaravelLocalization::getSupportedLanguagesKeys() as $locale) {
            $urls[$locale] = $resolver($locale);
        }

        return $urls;
    }

    private function renderUrl(string $loc, array $alternates, string $xDefault, string $changefreq, string $priority): string
    {
        $links = '';
        foreach ($alternates as $hreflang => $href) {
            $links .= '<xhtml:link rel="alternate" hreflang="'.e($hreflang).'" href="'.e($href).'"/>'."\n";
        }
        $links .= '<xhtml:link rel="alternate" hreflang="x-default" href="'.e($xDefault).'"/>'."\n";

        return '<url>'."\n"
            .'<loc>'.e($loc).'</loc>'."\n"
            .$links
            .'<changefreq>'.$changefreq.'</changefreq>'."\n"
            .'<priority>'.$priority.'</priority>'."\n"
            .'</url>'."\n";
    }
}
