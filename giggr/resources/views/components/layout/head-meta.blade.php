@props(['title' => null, 'description' => null])

@php
    use App\Support\LocalizedUrl;
    use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

    $siteName = config('app.name');
    $fullTitle = filled($title) ? $title.' — '.$siteName : $siteName;

    $routeName = request()->route()?->getName();
    $byRoute = __('seo.descriptions');
    $description = $description
        ?? (is_array($byRoute) ? ($byRoute[$routeName] ?? null) : null)
        ?? __('seo.description');

    $keywords = __('seo.keywords');
    $image = asset('images/summary_image.jpg');

    $locale = app()->getLocale();
    $locales = LaravelLocalization::getSupportedLanguagesKeys();
    $canonical = LocalizedUrl::for($locale);
    $regional = fn (string $l) => config("laravellocalization.supportedLocales.{$l}.regional", $l);
@endphp

<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">
<link rel="canonical" href="{{ $canonical }}">

@foreach ($locales as $alt)
    <link rel="alternate" hreflang="{{ $alt }}" href="{{ LocalizedUrl::for($alt) }}">
@endforeach
<link rel="alternate" hreflang="x-default" href="{{ LocalizedUrl::for(LaravelLocalization::getDefaultLocale()) }}">

<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $fullTitle }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:locale" content="{{ $regional($locale) }}">
@foreach ($locales as $alt)
    @unless ($alt === $locale)
        <meta property="og:locale:alternate" content="{{ $regional($alt) }}">
    @endunless
@endforeach

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $fullTitle }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">
