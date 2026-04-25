@props([
    'icon'  => '',
    'title' => '',
])

<article class="bg-white/[0.04] border border-white/8 rounded-2xl p-8 hover:bg-white/[0.07] hover:border-white/15 transition-colors duration-200">
    <div class="w-11 h-11 rounded-xl bg-accent/10 flex items-center justify-center mb-6">
        <x-icon :name="$icon" class="w-5 h-5 text-accent" />
    </div>
    <h3 class="font-heading text-2xl text-white mb-2">{{ $title }}</h3>
    <p class="text-lg text-white/45 leading-relaxed">{{ $slot }}</p>
</article>
