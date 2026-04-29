@props(['musician'])

<div class="relative h-52 md:h-64 bg-dark overflow-hidden" aria-hidden="true">

    {{-- grid bg --}}
    <div class="absolute inset-0 opacity-[0.04]"
         style="background-image: repeating-linear-gradient(0deg, currentColor, currentColor 1px, transparent 1px, transparent 40px), repeating-linear-gradient(90deg, currentColor, currentColor 1px, transparent 1px, transparent 40px); color: white;"></div>

    {{-- Back link --}}
    <div class="relative z-10 max-w-6xl mx-auto px-6 pt-6">
        <a
            href="{{ route('explore') }}"
            class="inline-flex items-center gap-1.5 text-sm text-bg/60 hover:text-bg transition-colors duration-150 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-bg/50 rounded"
        >
            {{ __('profile.back_to_explore') }}
        </a>
    </div>

    {{-- Name in banner --}}
    <div class="absolute bottom-6 left-0 right-0">
        <div class="max-w-6xl mx-auto px-6">
            <p class="text-bg/40 text-xs font-medium uppercase tracking-widest mb-1">{{ $musician['city'] }}</p>
            <h1 class="font-heading text-3xl md:text-4xl text-bg leading-tight">{{ $musician['name'] }}</h1>
        </div>
    </div>

</div>
