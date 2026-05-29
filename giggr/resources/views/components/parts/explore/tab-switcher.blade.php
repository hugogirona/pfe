@props(['activeTab' => 'profiles', 'profilesCount' => 0, 'announcementsCount' => 0])

<div class="inline-flex bg-dark/[0.06] rounded-xl p-1 gap-1" role="tablist">
    <a
        href="{{ route('explore', ['tab' => __('explore.tab_profiles_slug')]) }}"
        wire:navigate
        @class([
            'px-5 py-2 rounded-lg text-sm font-medium transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent',
            'bg-dark text-bg shadow-sm' => $activeTab === 'profiles',
            'text-dark/50 hover:text-dark' => $activeTab !== 'profiles',
        ])
        role="tab"
        aria-selected="{{ $activeTab === 'profiles' ? 'true' : 'false' }}"
    >
        {{ __('explore.tab_profiles') }}
        <span class="ml-1.5 text-xs opacity-60 tabular-nums" aria-hidden="true">({{ $profilesCount }})</span>
    </a>

    <a
        href="{{ route('explore', ['tab' => __('explore.tab_announcements_slug')]) }}"
        wire:navigate
        @class([
            'px-5 py-2 rounded-lg text-sm font-medium transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent',
            'bg-dark text-bg shadow-sm' => $activeTab === 'announcements',
            'text-dark/50 hover:text-dark' => $activeTab !== 'announcements',
        ])
        role="tab"
        aria-selected="{{ $activeTab === 'announcements' ? 'true' : 'false' }}"
    >
        {{ __('explore.tab_announcements') }}
        <span class="ml-1.5 text-xs opacity-60 tabular-nums" aria-hidden="true">({{ $announcementsCount }})</span>
    </a>
</div>
