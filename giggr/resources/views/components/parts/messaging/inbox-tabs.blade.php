@props(['activeTab'])

<div
    role="tablist"
    aria-label="{{ __('messaging.aria_tabs') }}"
    class="flex shrink-0 border-b border-dark/10"
>
    @foreach (['messages', 'requests'] as $tab)
        <button
            type="button"
            role="tab"
            id="messaging-tab-{{ $tab }}"
            aria-selected="{{ $activeTab === $tab ? 'true' : 'false' }}"
            aria-controls="messaging-inbox-panel"
            tabindex="{{ $activeTab === $tab ? '0' : '-1' }}"
            wire:click="switchTab('{{ $tab }}')"
            @class([
                'flex-1 py-4 text-sm font-medium transition-colors duration-150 cursor-pointer relative focus-visible:outline-none focus-visible:bg-dark/5',
                'text-dark' => $activeTab === $tab,
                'text-dark/40 hover:text-dark/70' => $activeTab !== $tab,
            ])
        >
            {{ __('messaging.tab_'.$tab) }}
            @if ($activeTab === $tab)
                <span class="absolute inset-x-6 bottom-0 h-0.5 bg-accent rounded-full" aria-hidden="true"></span>
            @endif
        </button>
    @endforeach
</div>
