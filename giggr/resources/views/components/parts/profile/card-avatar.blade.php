@props(['profile'])

@php
    $name  = $profile->user->full_name;
    $image = $profile->avatar_path;
@endphp

<div class="flex flex-col items-center pt-8 pb-4 px-6">
    <div class="relative">
        <div class="w-28 h-28 rounded-full overflow-hidden bg-pastel-blue ring-4 ring-bg shadow-md">
            @if ($image)
                <img
                    src="{{ Vite::asset('resources/img/profiles/' . $image) }}"
                    alt="{{ __('profile.avatar_alt', ['name' => $name]) }}"
                    class="w-full h-full object-cover object-center"
                />
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <span class="font-heading text-4xl text-dark/30 select-none">
                        {{ mb_substr($name, 0, 1) }}
                    </span>
                </div>
            @endif
        </div>

        <span class="absolute bottom-1 right-1 w-4 h-4 rounded-full bg-accent ring-2 ring-bg" aria-hidden="true"></span>
    </div>
</div>
