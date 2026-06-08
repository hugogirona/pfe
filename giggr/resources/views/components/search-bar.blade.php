@props([
    'placeholder' => null,
    'name' => 'q',
    'icon' => 'search',
    'label' => null,
    'shortcut' => true,
    'clearable' => true,
])

<div
    {{ $attributes->only('class')->class(['relative']) }}
    x-data="{ value: '', focused: false }"
    x-init="value = $refs.input.value"
    @if ($shortcut)
        @keydown.window.cmd.k.prevent="$refs.input.focus()"
        @keydown.window.ctrl.k.prevent="$refs.input.focus()"
    @endif
>
    @if ($label)
        <label for="{{ $name }}" class="sr-only">{{ $label }}</label>
    @endif

    <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center">
        <x-icon name="{{ $icon }}" class="w-4 h-4 text-caption" />
    </span>

    <input
        x-ref="input"
        id="{{ $name }}"
        type="search"
        name="{{ $name }}"
        placeholder="{{ $placeholder ?? __('home.search_placeholder') }}"
        autocomplete="off"
        @input="value = $event.target.value"
        @focus="focused = true"
        @blur="focused = false"
        @keydown.escape="value = ''; $el.blur()"
        {{ $attributes->except('class') }}
        class="w-full pl-10 pr-12 py-2.5 text-base bg-bg border border-dark/15 rounded-[6px] text-body placeholder:text-placeholder focus:outline-none focus:ring-1 focus:ring-accent transition-colors duration-150"
    />

    <div class="absolute inset-y-0 right-2 flex items-center">
        @if ($clearable)
            <button
                type="button"
                x-show="value.length > 0"
                x-cloak
                @click="value = ''; $refs.input.value = ''; $refs.input.dispatchEvent(new Event('input', { bubbles: true })); $refs.input.focus()"
                aria-label="{{ __('explore.search_clear') }}"
                class="flex items-center justify-center w-7 h-7 rounded-full text-caption hover:text-body hover:bg-dark/5 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
            >
                <x-icon name="x-mark" class="w-4 h-4" />
            </button>
        @endif

        @if ($shortcut)
            <kbd
                x-show="!focused && value.length === 0"
                x-cloak
                aria-hidden="true"
                class="pointer-events-none hidden sm:inline-flex items-center gap-0.5 h-6 px-1.5 rounded-md border border-dark/15 bg-dark/5 text-caption text-[0.6875rem] font-sans font-medium"
            >
                <x-icon name="command" class="w-3 h-3" />K
            </kbd>
        @endif
    </div>
</div>
