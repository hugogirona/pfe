@props(['musician'])

@if (!empty($musician['bio']))
    <section aria-labelledby="about-heading" class="bg-white rounded-2xl border border-dark/10 shadow-sm p-6 md:p-8">

        <h2 id="about-heading" class="font-heading text-2xl text-dark mb-4">
            {{ __('profile.about_title') }}
        </h2>

        <p class="text-dark/65 leading-relaxed text-[15px]">{{ $musician['bio'] }}</p>

    </section>
@endif
