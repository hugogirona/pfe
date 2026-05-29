@props(['activeTab' => 'profiles', 'profilesCount' => 0, 'announcementsCount' => 0, 'activeFiltersCount' => 0])

<div class="flex items-center justify-between gap-4 flex-wrap">
    <x-parts.explore.tab-switcher
        :active-tab="$activeTab"
        :profiles-count="$profilesCount"
        :announcements-count="$announcementsCount"
    />
    <x-parts.explore.toolbar :active-tab="$activeTab" :active-filters-count="$activeFiltersCount" />
</div>
