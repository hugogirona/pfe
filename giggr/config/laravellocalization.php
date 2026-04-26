<?php

return [

    'supportedLocales' => [
        'fr' => ['name' => 'French',  'script' => 'Latn', 'native' => 'français', 'regional' => 'fr_FR'],
        'en' => ['name' => 'English', 'script' => 'Latn', 'native' => 'English',  'regional' => 'en_GB'],
    ],

    'useAcceptLanguageHeader' => false,

    // FR is default: '/' = français, '/en' = english (no /fr prefix)
    'hideDefaultLocaleInURL' => true,

    'localesOrder'   => ['fr', 'en'],
    'localesMapping' => [],

    'utf8suffix' => env('LARAVELLOCALIZATION_UTF8SUFFIX', '.UTF-8'),

    'urlsIgnored' => [],

    'httpMethodsIgnored' => ['POST', 'PUT', 'PATCH', 'DELETE'],
];
