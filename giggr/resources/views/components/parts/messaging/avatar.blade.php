@props(['user'])

<div
    {{ $attributes->merge(['class' => 'rounded-full overflow-hidden bg-pastel-taupe text-body flex items-center justify-center font-semibold uppercase shrink-0']) }}
    aria-hidden="true"
>
    @if ($user?->profile?->thumbnail)
        <img src="{{ $user->profile->thumbnail }}" alt="" class="w-full h-full object-cover"/>
    @else
        <span>{{ $user ? mb_substr($user->full_name, 0, 1) : '?' }}</span>
    @endif
</div>
