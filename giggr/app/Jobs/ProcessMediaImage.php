<?php

namespace App\Jobs;

use App\Events\MediaProcessed;
use App\Models\Media;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;

class ProcessMediaImage implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Media $media,
        public readonly string $tmpPath,
        public readonly string $stem,
        public readonly ?string $replacedSource = null,
    ) {}

    public function handle(): void
    {
        $absolutePath = Storage::disk('local')->path($this->tmpPath);
        $config = config('media');

        try {
            foreach ($config['variants'] as $name => $size) {
                $encoded = Image::decodePath($absolutePath)
                    ->scaleDown($size['max_edge'], $size['max_edge'])
                    ->encode(new WebpEncoder(quality: $config['quality'], strip: true));

                Storage::disk($config['disk'])->put(
                    "{$config['base_dir']}/{$name}/{$this->stem}.webp",
                    (string) $encoded,
                );
            }

            $this->media->update([
                'source' => $this->stem,
                'processed_at' => now(),
            ]);

            if ($this->replacedSource !== null && $this->replacedSource !== $this->stem) {
                Media::deleteVariantsForSource($this->replacedSource);
            }

            broadcast(new MediaProcessed($this->media->fresh()));
        } finally {
            Storage::disk('local')->delete($this->tmpPath);
        }
    }
}
