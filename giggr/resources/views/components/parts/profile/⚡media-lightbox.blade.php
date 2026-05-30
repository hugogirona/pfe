<?php

use App\Enums\MediaType;
use App\Models\Media;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public bool $open = false;

    public ?int $mediaId = null;

    #[On('open-media-lightbox')]
    public function openLightbox(int $mediaId): void
    {
        $media = Media::find($mediaId);
        if ($media === null) {
            return;
        }

        $this->mediaId = $media->id;
        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
        $this->mediaId = null;
    }

    #[Computed]
    public function media(): ?Media
    {
        return $this->mediaId === null ? null : Media::find($this->mediaId);
    }
};
?>

<div
    x-data="{ show: $wire.entangle('open').live }"
    x-init="$watch('show', val => {
        if (val) {
            document.body.style.overflow = 'hidden';
        } else {
            setTimeout(() => document.body.style.overflow = '', 200);
            // Stop any playing YouTube embed by clearing its src.
            // display:none alone does not stop iframe audio/video.
            $el.querySelectorAll('iframe').forEach(f => { f.src = 'about:blank'; });
        }
    })"
    x-show="show"
    @keydown.escape.window="if (show) $wire.close()"
    class="fixed inset-0 z-[70] flex items-center justify-center p-4"
    style="display: none"
    role="dialog"
    aria-modal="true"
    aria-labelledby="media-lightbox-title"
>
    {{-- Backdrop --}}
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="$wire.close()"
        class="fixed inset-0 bg-dark/80 backdrop-blur-sm"
        aria-hidden="true"
    ></div>

    {{-- Card --}}
    @if ($this->media)
        @php $media = $this->media; @endphp
        <article
            x-show="show"
            x-transition:enter="transition ease-[cubic-bezier(0.34,1.56,0.64,1)] duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative z-10 w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden"
        >
            <h2 id="media-lightbox-title" class="sr-only">
                {{ $media->caption ?? __($media->type->label()) }}
            </h2>

            {{-- Close --}}
            <button
                wire:click="close"
                type="button"
                class="absolute top-2 right-2 z-20 w-11 h-11 flex items-center justify-center rounded-full bg-dark/50 hover:bg-dark/80 text-bg backdrop-blur-sm transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-accent"
                aria-label="{{ __('profile.lightbox_close') }}"
            >
                <x-icon name="x-mark" class="w-5 h-5"/>
            </button>

            {{-- Visual --}}
            <div class="flex-1 flex items-center justify-center min-h-0">
                @if ($media->type === MediaType::Image)
                    <img
                        src="{{ $media->display_url }}"
                        alt="{{ $media->caption ?? '' }}"
                        @if ($media->width && $media->height) style="aspect-ratio: {{ $media->width }} / {{ $media->height }};" @endif
                        class="max-w-full max-h-[80vh] object-contain rounded-lg shadow-2xl"
                    />
                @else
                    <div class="w-full aspect-video max-h-[80vh] rounded-lg overflow-hidden shadow-2xl bg-dark">
                        <iframe
                            src="{{ $media->display_url }}?autoplay=1&rel=0"
                            title="{{ $media->caption ?? __($media->type->label()) }}"
                            class="w-full h-full"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen
                        ></iframe>
                    </div>
                @endif
            </div>

            {{-- Caption --}}
            @if ($media->caption)
                <p class="mt-4 text-center text-sm text-bg/80 max-w-2xl mx-auto leading-relaxed">
                    {{ $media->caption }}
                </p>
            @endif
        </article>
    @endif
</div>
