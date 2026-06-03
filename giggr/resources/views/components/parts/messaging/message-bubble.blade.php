@props(['message', 'isMine', 'authorLabel'])

<div
    aria-label="{{ $authorLabel }}"
    @class([
        'max-w-[75%] px-3.5 py-2 text-sm leading-snug shadow-sm',
        'bg-accent text-on-dark rounded-2xl rounded-br-md' => $isMine,
        'bg-dark/8 text-body rounded-2xl rounded-bl-md' => ! $isMine,
    ])
>
    <p class="m-0 whitespace-pre-wrap wrap-break-word">{{ $message->body }}</p>

    <footer @class([
        'mt-1 flex items-center gap-1 text-[10px] leading-none',
        'justify-end text-on-dark-subtle' => $isMine,
        'justify-start text-caption' => ! $isMine,
    ])>
        <time datetime="{{ $message->created_at->toIso8601String() }}">
            {{ $message->created_at->format('H:i') }}
        </time>
        @if ($isMine)
            <span
                class="ml-0.5"
                aria-label="{{ $message->read_at ? __('messaging.read') : __('messaging.sent') }}"
                title="{{ $message->read_at ? __('messaging.read') : __('messaging.sent') }}"
            >{{ $message->read_at ? '✓✓' : '✓' }}</span>
        @endif
    </footer>
</div>
