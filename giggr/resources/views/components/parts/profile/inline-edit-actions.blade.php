<div class="flex items-center justify-end gap-2 text-xs text-dark/45 pt-1">
    <button
        type="button"
        {{ $cancel->attributes->class(['hover:text-dark transition-colors duration-150 cursor-pointer underline-offset-4 hover:underline focus-visible:outline-none focus-visible:underline']) }}
    >{{ $cancel }}</button>
    <span aria-hidden="true">·</span>
    <button
        type="button"
        {{ $save->attributes->class(['text-dark transition-colors duration-150 cursor-pointer underline-offset-4 hover:underline focus-visible:outline-none focus-visible:underline']) }}
    >{{ $save }}</button>
</div>
