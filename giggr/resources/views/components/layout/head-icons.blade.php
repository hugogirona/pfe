<link rel="icon" type="image/png" href="{{ Vite::asset('resources/favicon/favicon-96x96.png') }}" sizes="96x96">
<link rel="icon" type="image/svg+xml" href="{{ Vite::asset('resources/favicon/favicon.svg') }}">
<link rel="shortcut icon" href="/favicon.ico">
<link rel="apple-touch-icon" sizes="180x180" href="{{ Vite::asset('resources/favicon/apple-touch-icon.png') }}">
<meta name="apple-mobile-web-app-title" content="Giggr.">
<link rel="manifest" href="{{ route('site.webmanifest') }}">

@production
    <link rel="preload" href="{{ Vite::asset('resources/fonts/dm-sans-regular.woff2') }}" as="font" type="font/woff2" crossorigin>
@endproduction
