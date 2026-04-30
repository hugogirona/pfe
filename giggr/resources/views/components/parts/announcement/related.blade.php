@props(['suggestions'])

@if ($suggestions->isNotEmpty())
    <section aria-labelledby="related-heading" class="border-t border-dark/10 pt-10">

        <h2 id="related-heading" class="font-heading text-2xl md:text-3xl text-dark mb-6">
            {{ __('announcement.related_title') }}
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($suggestions as $suggestion)
                <x-parts.explore.announcement-card :announcement="$suggestion" />
            @endforeach
        </div>

    </section>
@endif
