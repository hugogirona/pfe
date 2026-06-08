<?php

it('renders SEO meta tags in the head of public pages', function () {
    $html = $this->get('/')->assertOk()->getContent();

    expect($html)
        ->toContain('<meta name="description" content="'.e(__('seo.descriptions.home')).'"')
        ->toContain('<meta name="keywords" content="'.e(__('seo.keywords')).'"')
        ->toContain('<link rel="canonical"')
        ->toContain('hreflang="fr"')
        ->toContain('hreflang="en"')
        ->toContain('hreflang="nl"')
        ->toContain('hreflang="x-default"')
        ->toContain('property="og:title"')
        ->toContain('property="og:description"')
        ->toContain('property="og:url"')
        ->toContain('property="og:locale"')
        ->toContain('name="twitter:card"');
});

it('localizes the explore tab slug in hreflang alternates', function () {
    $html = $this->get('/explorer/'.__('explore.tab_announcements_slug'))->assertOk()->getContent();

    expect($html)
        ->toContain('hreflang="en" href="'.url('/en/explore/'.__('explore.tab_announcements_slug', [], 'en')).'"')
        ->toContain('hreflang="nl" href="'.url('/nl/ontdekken/'.__('explore.tab_announcements_slug', [], 'nl')).'"')
        ->not->toContain('/explore/'.__('explore.tab_announcements_slug'));
});
