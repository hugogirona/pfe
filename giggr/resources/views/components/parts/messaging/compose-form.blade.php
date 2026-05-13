@props([
    'conversationId' => null,
    'blockState' => null,
])

@if ($blockState !== null)
    <div class="px-6 py-5 border-t border-dark/10 shrink-0 bg-dark/3 text-center" role="status">
        <p class="text-sm text-dark/60 italic leading-relaxed">
            @if ($blockState === 'blocked-by-me')
                {{ __('messaging.blocked_by_you') }}
            @else
                {{ __('messaging.blocked_by_them') }}
            @endif
        </p>
    </div>
@else
    <form
        wire:submit="send"
        aria-label="{{ __('messaging.compose_aria') }}"
        class="px-4 py-3 border-t border-dark/10 shrink-0 bg-bg"
        x-data="{
            emitTyping() {
                const id = @js($conversationId);
                if (! id || typeof window.Echo === 'undefined') return;
                window.Echo.private('conversation.' + id).whisper('typing', {});
            },
        }"
    >
        <label for="messaging-compose-body" class="sr-only">
            {{ __('messaging.compose_label') }}
        </label>

        <div class="relative rounded-xl bg-dark/5 has-[textarea:focus]:bg-bg has-[textarea:focus]:ring-1 has-[textarea:focus]:ring-accent transition-colors duration-150">
            <textarea
                id="messaging-compose-body"
                name="body"
                wire:model="body"
                rows="2"
                placeholder="{{ __('messaging.compose_placeholder') }}"
                class="block w-full max-h-60 resize-none scrollbar-none bg-transparent pl-4 pr-12 py-2 text-sm leading-6 text-dark placeholder-dark/40 focus:outline-none"
                @keydown.enter.prevent="if (! $event.shiftKey) $wire.send()"
                @focus.once="$wire.dismissNewMessageMarker()"
                @input.throttle.2000ms="emitTyping()"
            ></textarea>

            <button
                type="submit"
                wire:loading.attr="disabled"
                aria-label="{{ __('messaging.send') }}"
                class="absolute bottom-1.5 right-1.5 w-9 h-9 flex items-center justify-center rounded-lg text-accent hover:bg-accent/10 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent disabled:opacity-40 disabled:cursor-not-allowed"
            >
                <x-icon name="arrow-right" class="w-4 h-4 -rotate-45"/>
            </button>
        </div>
    </form>
@endif
