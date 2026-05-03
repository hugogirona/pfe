@props(['name', 'required' => false])

<div>
    <label for="{{ $name }}" class="flex items-start gap-3 cursor-pointer group">
        <div class="relative shrink-0 mt-0.5 w-5 h-5">
            <input
                type="checkbox"
                id="{{ $name }}"
                name="{{ $name }}"
                @if($required) required aria-required="true" @endif
                {{ $attributes }}
                class="peer sr-only"
            />

            <div class="absolute inset-0 rounded-sm border-2 border-dark/20 bg-white
                        peer-checked:bg-accent peer-checked:border-accent
                        peer-focus-visible:ring-2 peer-focus-visible:ring-offset-1 peer-focus-visible:ring-accent/50
                        group-hover:border-dark/40 peer-checked:group-hover:border-accent
                        transition-all duration-150 pointer-events-none">
            </div>

            <span class="absolute inset-0 flex items-center justify-center pointer-events-none
                         opacity-0 peer-checked:opacity-100 transition-opacity duration-150"
                  aria-hidden="true">
                <x-icon name="check" class="w-3 h-3 text-white" />
            </span>
        </div>

        <span class="text-sm text-dark/60 leading-relaxed select-none">
            {!! $slot !!}
        </span>
    </label>
</div>
