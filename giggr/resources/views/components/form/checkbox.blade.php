@props(['name', 'required' => false])

<div>
    <label for="{{ $name }}" class="flex items-start gap-3 cursor-pointer group">
        <span class="relative block shrink-0 mt-0.5 w-5 h-5">
            <input
                type="checkbox"
                id="{{ $name }}"
                name="{{ $name }}"
                @if($required) required @endif
                @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror
                {{ $attributes }}
                class="peer sr-only"
            />

            <span @class([
                'absolute inset-0 rounded-sm border-2 bg-white',
                'peer-checked:bg-accent peer-checked:border-accent',
                'peer-focus-visible:ring-1 peer-focus-visible:ring-accent',
                'group-hover:border-dark/40 peer-checked:group-hover:border-accent',
                'transition-all duration-150 pointer-events-none',
                'border-danger/60' => $errors->has($name),
                'border-dark/20' => ! $errors->has($name),
            ])>
            </span>

            <span class="absolute inset-0 flex items-center justify-center pointer-events-none
                         opacity-0 peer-checked:opacity-100 transition-opacity duration-150"
                  aria-hidden="true">
                <x-icon name="check" class="w-3 h-3 text-white" />
            </span>
        </span>

        <span class="text-sm text-dark/60 leading-relaxed select-none">
            {!! $slot !!}
        </span>
    </label>
    @error($name)
        <p id="{{ $name }}-error" role="alert" class="text-xs text-danger mt-1 ml-8">{{ $message }}</p>
    @enderror
</div>
