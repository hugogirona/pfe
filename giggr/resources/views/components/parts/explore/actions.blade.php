@props(['musiciansCount' => 0, 'announcementsCount' => 0, 'activeFiltersCount' => 0])

<div class="flex items-center justify-between gap-4 flex-wrap">
    <x-parts.explore.tab-switcher
        :musicians-count="$musiciansCount"
        :announcements-count="$announcementsCount"
    />
    <x-parts.explore.toolbar :active-filters-count="$activeFiltersCount" />
</div>
