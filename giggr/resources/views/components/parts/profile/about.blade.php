@props(['profile', 'isOwner' => false])

@if ($profile->bio || $isOwner)
    <section
        aria-labelledby="about-heading"
        class="bg-white rounded-2xl border border-dark/10 shadow-sm p-6 md:p-8"
    >
        <x-parts.profile.bio-editor :bio="$profile->bio" :isOwner="$isOwner" />
    </section>
@endif
