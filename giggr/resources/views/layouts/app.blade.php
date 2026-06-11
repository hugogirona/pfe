<!DOCTYPE html>
<html class="overscroll-none" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ filled($title ?? null) ? $title.' — '.config('app.name') : config('app.name') }}</title>

    <x-layout.head-meta :title="$title ?? null" :description="$description ?? null" />

    <x-layout.head-icons />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <noscript>
        <style>[data-js-only] { display: none !important; }</style>
    </noscript>
</head>
<body class="bg-bg text-body font-sans antialiased flex flex-col min-h-screen">

<a href="#main"
   class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-100 focus:px-4 focus:py-2 focus:rounded-lg focus:bg-dark focus:text-on-dark focus:outline-none focus:ring-2 focus:ring-accent-on-dark">
    {{ __('nav.skip_to_content') }}
</a>

<x-header :heading="$title ?? null"/>

<main id="main" class="flex-1">
    {{ $slot }}
</main>

<x-footer/>

<livewire:modal/>
<livewire:auth-modal/>

@livewireScripts
</body>
</html>
