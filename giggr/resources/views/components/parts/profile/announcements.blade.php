@props(['profile', 'isOwner' => false])

<section aria-labelledby="announcements-heading" class="bg-white rounded-2xl border border-dark/10 shadow-sm p-6 md:p-8">

    <div class="flex items-center justify-between mb-6">
        <h2 id="announcements-heading" class="font-heading text-2xl text-dark">
            {{ __('profile.announcements_title') }}
        </h2>
        @if ($isOwner)
            <x-cta
                variant="simple"
                size="icon"
                @click="$wire.dispatch('open-modal', { component: 'parts.announcement.form', title: '{{ __('announcement.create_title') }}' })"
                aria-label="{{ __('announcement.create_title') }}"
            >
                <x-icon name="plus" class="w-4 h-4" />
            </x-cta>
        @endif
    </div>

    @if ($profile->user->announcements->isNotEmpty())

        <div class="space-y-3">
            @foreach ($profile->user->announcements as $announcement)
                <x-parts.explore.announcement-card :announcement="$announcement" />
            @endforeach
        </div>

    @else

        <p class="text-sm text-dark/40 italic">
            {{ $isOwner ? __('profile.announcements_empty_owner') : __('profile.announcements_empty', ['name' => $profile->user->full_name]) }}
        </p>
    @endif

</section>
