<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="preload" href="/fonts/dm-sans-regular.woff2" as="font" type="font/woff2" crossorigin>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-bg text-dark font-sans antialiased flex flex-col min-h-screen">

    @persist('header')
        <x-header />
    @endpersist

    <main class="flex-1">
        {{ $slot }}
    </main>

    @persist('footer')
        <x-footer />
    @endpersist

    <livewire:modal />

    @livewireScripts
</body>
</html>
