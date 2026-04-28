@props(['question', 'number' => '01', 'open' => false])

<div x-data="{ open: {{ $open ? 'true' : 'false' }} }"
     class="group border-b border-dark/10 last:border-b-0">

    <button
        @click="open = !open"
        aria-expanded="{{ $open ? 'true' : 'false' }}"
        :aria-expanded="open.toString()"
        class="flex items-center gap-4 w-full py-5 text-left cursor-pointer
               focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/40
               focus-visible:ring-offset-2 rounded-sm">

        <span class="font-heading text-sm tabular-nums transition-colors duration-200 shrink-0"
              :class="open ? 'text-accent' : 'text-dark/20 group-hover:text-dark/40'">
            {{ $number }}
        </span>

        <span class="flex-1 font-medium text-dark leading-snug">
            {{ $question }}
        </span>

        <x-icon name="chevron-down"
                class="w-4 h-4 shrink-0 text-dark/25 group-hover:text-dark/50 transition-all duration-200"
                x-bind:class="open ? '-rotate-180' : 'rotate-0'" />
    </button>

    <div class="grid transition-[grid-template-rows] duration-200 ease-out"
         :class="open ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'">
        <div class="overflow-hidden">
            <div class="pt-0 pb-6 pl-9 text-dark/55 leading-relaxed text-sm md:text-base">
                {!! $slot !!}
            </div>
        </div>
    </div>

</div>
