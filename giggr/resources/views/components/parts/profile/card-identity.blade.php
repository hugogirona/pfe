@props([
    'profile',
    'isOwner'        => false,
    'allStatuses'    => [],
    'selectedStatus' => '',
])

@php
    $name        = $profile->user->full_name;
    $statusLabel = __($profile->status->label());
@endphp

<section aria-labelledby="identity-name-heading" class="text-center px-6 pb-5 border-b border-dark/[0.07]">
    <h3 id="identity-name-heading" class="font-heading text-2xl text-heading leading-tight" itemprop="name">{{ $name }}</h3>

    @if ($profile->city)
        <p class="text-sm text-subtle mt-1 flex items-center justify-center gap-1"
           itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
            <x-icon name="map-pin" class="w-3.5 h-3.5"/>
            <span itemprop="addressLocality">{{ $profile->city->name }}</span>
        </p>
    @endif

    @if ($isOwner)
        <div x-data="{
                open: false,
                focused: -1,
                options: @js($allStatuses),
                openList() {
                    this.open = true;
                    this.focused = this.options.findIndex(o => o.value === $wire.selectedStatus);
                    if (this.focused < 0) this.focused = 0;
                },
                closeList() {
                    this.open = false;
                    this.focused = -1;
                },
                pick(value) {
                    $wire.saveStatus(value);
                    this.closeList();
                    $refs.trigger.focus();
                },
                focusNext() {
                    if (!this.open) { this.openList(); return; }
                    this.focused = (this.focused + 1) % this.options.length;
                },
                focusPrev() {
                    if (!this.open) { this.openList(); return; }
                    this.focused = (this.focused - 1 + this.options.length) % this.options.length;
                },
                confirmFocused() {
                    if (this.focused >= 0) this.pick(this.options[this.focused].value);
                },
             }"
             @keydown.escape.prevent.stop="closeList(); $refs.trigger.focus()"
             @click.outside="closeList()"
             class="mt-3">

            <div class="flex items-center justify-center gap-2">
                <span class="text-xs uppercase tracking-wider text-subtle font-medium">
                    {{ $statusLabel }}
                </span>
                <button
                    type="button"
                    x-ref="trigger"
                    @click="open ? closeList() : openList()"
                    @keydown.arrow-down.prevent="focusNext()"
                    @keydown.arrow-up.prevent="focusPrev()"
                    @keydown.enter.prevent="open ? confirmFocused() : openList()"
                    @keydown.space.prevent="open ? confirmFocused() : openList()"
                    role="combobox"
                    aria-haspopup="listbox"
                    aria-controls="profile-status-listbox"
                    :aria-expanded="open"
                    :aria-activedescendant="open && focused >= 0 ? `profile-status-option-${options[focused].value}` : null"
                    aria-label="{{ __('profile.edit_status') }}"
                    class="p-1 text-caption hover:text-subtle transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent focus-visible:text-subtle rounded"
                >
                    <x-icon name="pencil-square" class="w-3.5 h-3.5"/>
                </button>
            </div>

            <div class="grid motion-safe:transition-[grid-template-rows] motion-safe:duration-150 motion-safe:ease-out"
                 :class="open ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'">
                <div class="overflow-hidden">
                    <ul
                        id="profile-status-listbox"
                        role="listbox"
                        aria-label="{{ __('profile.status_label') }}"
                        class="mt-2 border border-dark/10 rounded-sm overflow-hidden bg-bg text-left"
                    >
                        <template x-for="(option, index) in options" :key="option.value">
                            <li
                                :id="`profile-status-option-${option.value}`"
                                role="option"
                                :aria-selected="$wire.selectedStatus === option.value"
                                @click="pick(option.value)"
                                @mouseenter="focused = index"
                                :class="[
                                    focused === index ? 'bg-dark/[0.04]' : '',
                                    $wire.selectedStatus === option.value ? 'text-body font-medium' : 'text-subtle',
                                ]"
                                class="px-3 py-2 text-sm cursor-pointer transition-colors duration-100"
                            >
                                <span x-text="option.label"></span>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>
    @elseif ($statusLabel)
        <span class="inline-block mt-3 text-xs uppercase tracking-wider text-subtle font-medium">
            {{ $statusLabel }}
        </span>
    @endif
</section>
