<div class="hidden md:flex items-center gap-8">
    <x-nav.link href="{{ route('home') }}">{{ __('nav.home') }}</x-nav.link>
    <x-nav.link href="{{ route('explore') }}" :exact="false">{{ __('nav.explore') }}</x-nav.link>
    <x-nav.link href="{{ route('contact') }}">{{ __('nav.contact') }}</x-nav.link>
</div>
