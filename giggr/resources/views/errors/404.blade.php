<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — {{ config('app.name') }}</title>
    <link rel="preload" href="/fonts/dm-sans-regular.woff2" as="font" type="font/woff2" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-bg text-dark font-sans antialiased flex flex-col min-h-screen">
    <h1 class="sr-only">404</h1>
    <x-nav />
    <main class="flex-1 flex flex-col items-center justify-center px-6 py-16 md:py-24">
        <div class="flex items-center justify-center" role="img" aria-label="404">
            <img
                src="{{ Vite::asset('resources/img/404.svg') }}"
                alt=""
                aria-hidden="true"
                class="relative z-10 h-[28vw] md:h-[22vw] lg:h-[18vw] w-auto -mx-3 md:-mx-6"
            />
        </div>

        <p class="font-heading text-3xl text-center md:text-5xl py-16 md:py-8 text-dark">
            {{ __('errors.not_found_title') }}
        </p>

        <x-cta variant="dark" size="lg" href="{{ LaravelLocalization::getLocalizedURL(app()->getLocale(), '/') }}">
            {{ __('errors.back_home') }}
        </x-cta>
    </main>
    <x-footer />
    @livewireScripts
</body>
</html>
