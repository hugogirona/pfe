@props(['musician'])

<section aria-labelledby="announcements-heading" class="bg-white rounded-2xl border border-dark/10 shadow-sm p-6 md:p-8">

    <h2 id="announcements-heading" class="font-heading text-2xl text-dark mb-6">
        {{ __('profile.announcements_title') }}
    </h2>

    @if (!empty($musician['announcements']))

        <div class="space-y-3">
            @foreach ($musician['announcements'] as $announcement)
                <x-parts.explore.announcement-card :announcement="$announcement" />
            @endforeach
        </div>

    @else

        <p class="text-sm text-dark/40 italic">
            {{ __('profile.announcements_empty', ['name' => $musician['name']]) }}
        </p>

    @endif

</section>
