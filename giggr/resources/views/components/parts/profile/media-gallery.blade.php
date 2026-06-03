@props(['profile', 'isOwner' => false])

<section
    aria-labelledby="gallery-heading"
    class="bg-white rounded-2xl border border-dark/10 shadow-sm p-6 md:p-8"
    x-data="{ editMode: false }"
>

    <div class="flex items-center justify-between mb-6">
        <h2 id="gallery-heading" class="font-heading text-2xl text-heading">
            {{ __('profile.gallery_title') }}
        </h2>
        @if ($isOwner && $profile->media->isNotEmpty())
            <div class="flex items-center gap-2">
                <x-cta
                    variant="simple"
                    size="icon"
                    @click="editMode = !editMode"
                    x-bind:class="editMode ? 'bg-accent/15 ring-1 ring-accent text-accent' : ''"
                    x-bind:aria-pressed="editMode"
                    aria-label="{{ __('profile.gallery_edit_mode') }}"
                >
                    <x-icon name="pencil-square" class="w-4 h-4"/>
                </x-cta>

                <x-parts.profile.add-media-menu :profile="$profile" x-show="!editMode"/>
            </div>
        @elseif ($isOwner)
            <x-parts.profile.add-media-menu :profile="$profile"/>
        @endif
    </div>

    @if ($isOwner && $profile->media->isNotEmpty())
        <p x-show="editMode" x-cloak class="text-xs text-accent mb-4 italic">
            {{ __('profile.gallery_edit_hint_owner') }}
        </p>
    @endif

    @if ($profile->media->isEmpty())
        <p class="text-sm text-caption italic">
            {{ $isOwner ? __('profile.gallery_empty_owner') : __('profile.gallery_empty') }}
        </p>
    @else
        @php $musicianName = $profile->user->full_name; @endphp
        <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($profile->media as $media)
                <li>
                    <x-parts.profile.media-item
                        :media="$media"
                        :musician-name="$musicianName"
                        :can-edit="$isOwner"
                    />
                </li>
            @endforeach
        </ul>
    @endif

</section>
