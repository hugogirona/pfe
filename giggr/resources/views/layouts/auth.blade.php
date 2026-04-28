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
<body class="bg-bg text-dark font-sans antialiased">
    <div class="min-h-screen flex flex-col md:flex-row-reverse">

        <div class="flex-1 flex flex-col relative">
        
            <div class="absolute top-5 right-6 z-10">
                <x-footer.lang-switcher variant="light" />
            </div>
            
            <div class="md:hidden px-6 pt-10 pb-2">
                <a href="{{ route('home') }}" aria-label="Giggr.">
                    <x-logo class="h-7 w-auto text-dark" />
                </a>
            </div>

            <main class="flex-1 flex items-start justify-center md:justify-start px-8 md:px-14 lg:px-16 py-10 md:pt-[22vh]">
                <div class="w-full max-w-[450px]">
                    {{ $slot }}
                </div>
            </main>

        </div>

         <div class="hidden md:flex md:w-[42%] bg-dark/5 flex-col min-h-screen sticky top-0 relative" aria-hidden="true">
            <div class="absolute top-0 left-0 p-10">
                <a href="{{ route('home') }}" aria-label="Giggr.">
                    <x-logo class="h-7 w-auto text-dark" />
                </a>
            </div>
            <div class="flex-1 flex flex-col px-8 md:px-14 lg:px-16 pt-[22vh] pb-10">
                <div class="text-right">
                    <section>
                        <h2 class="text-accent text-sm font-medium uppercase tracking-widest mb-5">{{ __('auth.panel_eyebrow') }}</h2>
                        <p class="text-dark/50 text-base leading-relaxed mb-5">{{ __('auth.panel_subtitle') }}</p>
                    </section>
                </div>
            </div>
        </div>
    @livewireScripts
</body>
</html>
