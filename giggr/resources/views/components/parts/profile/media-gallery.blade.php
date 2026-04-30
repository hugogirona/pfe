@props(['profile'])

<section aria-labelledby="gallery-heading" class="bg-white rounded-2xl border border-dark/10 shadow-sm p-6 md:p-8">

    <h2 id="gallery-heading" class="font-heading text-2xl text-dark mb-6">
        {{ __('profile.gallery_title') }}
    </h2>

    <p class="text-sm text-dark/40 italic">{{ __('profile.gallery_empty') }}</p>

</section>
