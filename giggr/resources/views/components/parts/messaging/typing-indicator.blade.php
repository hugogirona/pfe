@props(['name'])

<div
    x-show="isTyping"
    x-transition.opacity.duration.200ms
    class="flex justify-start mt-2"
    aria-live="polite"
    aria-atomic="true"
>
    <div class="inline-flex items-center gap-1 px-3.5 py-2.5 rounded-2xl rounded-bl-md bg-dark/8 shadow-sm" role="status">
        <span class="sr-only">{{ __('messaging.typing_aria', ['name' => $name]) }}</span>
        <span class="typing-dot w-1.5 h-1.5 bg-dark/50 rounded-full" aria-hidden="true"></span>
        <span class="typing-dot w-1.5 h-1.5 bg-dark/50 rounded-full" style="animation-delay: 0.15s" aria-hidden="true"></span>
        <span class="typing-dot w-1.5 h-1.5 bg-dark/50 rounded-full" style="animation-delay: 0.3s" aria-hidden="true"></span>
    </div>
</div>
