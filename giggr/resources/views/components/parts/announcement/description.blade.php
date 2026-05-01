@props(['announcement'])

@if ($announcement->description)
    <section aria-labelledby="description-heading" class="bg-white rounded-2xl border border-dark/10 shadow-sm p-6 md:p-8">

        <h2 id="description-heading" class="font-heading text-2xl text-dark mb-5">
            {{ __('announcement.description_title') }}
        </h2>

        <div class="prose-custom">
            <p class="text-dark/65 leading-relaxed text-[15px] whitespace-pre-line">{{ $announcement->description }}</p>
        </div>

    </section>
@endif
