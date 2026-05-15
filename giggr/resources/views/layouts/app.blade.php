<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="icon" type="image/png" href="/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
    <meta name="apple-mobile-web-app-title" content="Giggr.">
    <link rel="manifest" href="/favicon/site.webmanifest">

    @production
        <link rel="preload" href="/fonts/dm-sans-regular.woff2" as="font" type="font/woff2" crossorigin>
    @endproduction

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-bg text-dark font-sans antialiased flex flex-col min-h-screen">

<x-header/>

<main class="flex-1">
    {{ $slot }}
</main>

<x-footer/>

<livewire:modal/>
<livewire:auth-modal/>

@livewireScripts
</body>
</html>
