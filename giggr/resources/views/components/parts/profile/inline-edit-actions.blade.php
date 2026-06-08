<div class="flex items-center justify-end gap-2 text-xs text-caption pt-1">
    <button
        type="button"
        {{ $cancel->attributes->class(['hover:text-danger text-danger/80 transition-colors duration-150 cursor-pointer underline-offset-4 hover:underline focus-visible:outline-none focus-visible:underline']) }}
    >{{ $cancel }}</button>
    <span aria-hidden="true">·</span>
    <button
        type="button"
        {{ $save->attributes->class(['text-body text-bold transition-colors duration-150 cursor-pointer underline-offset-4 hover:underline focus-visible:outline-none focus-visible:underline']) }}
    >{{ $save }}</button>
</div>
