@props(['profile'])

<section aria-labelledby="announcements-heading" class="bg-white rounded-2xl border border-dark/10 shadow-sm p-6 md:p-8">

    <h2 id="announcements-heading" class="font-heading text-2xl text-dark mb-6">
        {{ __('profile.announcements_title') }}
    </h2>

    @if ($profile->user->announcements->isNotEmpty())

        <div class="space-y-3">
            @foreach ($profile->user->announcements as $announcement)
                <x-parts.explore.announcement-card :announcement="$announcement" />
            @endforeach
        </div>

    @else

        <p class="text-sm text-dark/40 italic">
            {{ __('profile.announcements_empty', ['name' => $profile->user->full_name]) }}
        </p>

    @endif

</section>
