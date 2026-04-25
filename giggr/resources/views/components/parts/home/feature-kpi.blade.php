@props([
    'value' => '',
    'label' => '',
])

<div class="bg-white/[0.04] border border-white/8 rounded-2xl py-7 px-4 flex flex-col items-center justify-center text-center">
    <span class="font-heading text-3xl md:text-4xl font-bold text-white">{{ $value }}</span>
    <span class="text-base text-white/35 mt-2 tracking-wide uppercase">{{ $label }}</span>
</div>
