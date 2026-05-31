<!DOCTYPE html>
<html class="overscroll-none" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ filled($title ?? null) ? $title.' — '.config('app.name') : config('app.name') }}</title>

    <link rel="icon" type="image/png" href="{{ Vite::asset('resources/favicon/favicon-96x96.png') }}" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="{{ Vite::asset('resources/favicon/favicon.svg') }}">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ Vite::asset('resources/favicon/apple-touch-icon.png') }}">
    <meta name="apple-mobile-web-app-title" content="Giggr.">
    <link rel="manifest" href="{{ route('site.webmanifest') }}">

    @production
        <link rel="preload" href="{{ Vite::asset('resources/fonts/dm-sans-regular.woff2') }}" as="font" type="font/woff2" crossorigin>
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
