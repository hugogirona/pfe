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
