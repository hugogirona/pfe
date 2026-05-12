@props(['message', 'isMine', 'authorLabel'])

<article
    aria-label="{{ $authorLabel }}"
    @class([
        'max-w-[75%] px-3.5 py-2 text-sm leading-snug shadow-sm',
        'bg-accent text-bg rounded-2xl rounded-br-md' => $isMine,
        'bg-dark/8 text-dark rounded-2xl rounded-bl-md' => ! $isMine,
    ])
>
    {{ $message->body }}
</article>
