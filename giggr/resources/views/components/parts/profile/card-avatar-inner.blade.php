@if ($profile->thumbnail)
    <img
        src="{{ $profile->thumbnail }}"
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
