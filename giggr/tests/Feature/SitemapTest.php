<?php

use App\Support\SitemapGenerator;

it('generates a well-formed multilingual sitemap', function () {
    $xml = app(SitemapGenerator::class)->toXml();

    expect(simplexml_load_string($xml))->not->toBeFalse()
        ->and($xml)->toContain('<urlset')
        ->and($xml)->toContain('hreflang="fr"')
        ->and($xml)->toContain('hreflang="en"')
        ->and($xml)->toContain('hreflang="nl"')
        ->and($xml)->toContain('hreflang="x-default"');
});

it('includes public pages and excludes authenticated areas', function () {
    $xml = app(SitemapGenerator::class)->toXml();

    expect($xml)
        ->toContain('/contact')
        ->toContain('/inscription')
        ->not->toContain('/parametres')
        ->not->toContain('/profil/')
        ->not->toContain('/annonces/');
});

it('writes the sitemap to a file via the command', function () {
    $path = storage_path('framework/testing/sitemap-test.xml');

    $this->artisan('sitemap:generate', ['path' => $path])->assertSuccessful();

    expect(file_exists($path))->toBeTrue()
        ->and(simplexml_load_string(file_get_contents($path)))->not->toBeFalse();

    @unlink($path);
});
