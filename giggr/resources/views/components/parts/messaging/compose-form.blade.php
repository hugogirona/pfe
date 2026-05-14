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
            typingCooldown: null,
            maxLines: 4,
            autosize(el) {
                el.style.height = 'auto';
                const styles = window.getComputedStyle(el);
                const lineHeight = parseFloat(styles.lineHeight) || 20;
                const paddingY = parseFloat(styles.paddingTop) + parseFloat(styles.paddingBottom);
                const maxH = lineHeight * this.maxLines + paddingY;
                el.style.height = Math.min(el.scrollHeight, maxH) + 'px';
                el.style.overflowY = el.scrollHeight > maxH ? 'auto' : 'hidden';
            },
            reset(el) {
                el.style.height = '';
                el.style.overflowY = 'hidden';
            },
            emitTyping() {
                if (this.typingCooldown) return;
                const id = @js($conversationId);
                if (! id || typeof window.Echo === 'undefined') return;
                window.Echo.private('conversation.' + id).whisper('typing', {});
                this.typingCooldown = setTimeout(() => { this.typingCooldown = null; }, 2000);
            },
            threadUpdatedCleanup: null,
            init() {
                this.$nextTick(() => {
                    const ta = this.$refs.textarea;
                    if (ta && ta.value) this.autosize(ta);
                });
                this.threadUpdatedCleanup = this.$wire.on('thread-updated', () => {
                    if (this.$refs.textarea) this.reset(this.$refs.textarea);
                });
            },
            destroy() {
                if (typeof this.threadUpdatedCleanup === 'function') {
                    this.threadUpdatedCleanup();
                }
            },
        }"
    >
        <label for="messaging-compose-body" class="sr-only">
            {{ __('messaging.compose_label') }}
        </label>

        <div class="flex items-end gap-2">
            <div class="flex-1 min-w-0 rounded-3xl bg-dark/5 has-[textarea:focus]:bg-bg has-[textarea:focus]:ring-1 has-[textarea:focus]:ring-accent transition-colors duration-150">
                <textarea
                    id="messaging-compose-body"
                    name="body"
                    wire:model="body"
                    rows="1"
                    placeholder="{{ __('messaging.compose_placeholder') }}"
                    class="block w-full resize-none scrollbar-none bg-transparent px-4 py-2.5 text-sm leading-5 text-dark placeholder-dark/40 focus:outline-none"
                    style="overflow-y: hidden;"
                    x-ref="textarea"
                    @keydown.enter.prevent="if (! $event.shiftKey) $wire.send()"
                    @focus.once="$wire.dismissNewMessageMarker()"
                    @input="autosize($el); emitTyping()"
                ></textarea>
            </div>

            <button
                type="submit"
                wire:loading.attr="disabled"
                aria-label="{{ __('messaging.send') }}"
                class="w-10 h-10 shrink-0 rounded-full bg-accent text-bg flex items-center justify-center hover:opacity-90 transition-opacity duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-1 disabled:opacity-40 disabled:cursor-not-allowed"
            >
                <x-icon name="arrow-right" class="w-4 h-4 -rotate-45"/>
            </button>
        </div>
    </form>
@endif
