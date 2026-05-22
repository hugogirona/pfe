<?php

namespace App\Jobs;

use App\Events\AvatarProcessed;
use App\Models\Profile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;

class ProcessAvatarImage implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Profile $profile,
        public readonly string $tmpPath,
        public readonly string $stem,
    ) {}

    public function handle(): void
    {
        $absolutePath = Storage::disk('local')->path($this->tmpPath);
        $config = config('avatars');

        try {
            foreach ($config['variants'] as $name => $size) {
                $encoded = Image::decodePath($absolutePath)
                    ->cover($size['width'], $size['height'])
                    ->encode(new WebpEncoder(quality: $config['quality'], strip: true));

                Storage::disk($config['disk'])->put(
                    "{$config['base_dir']}/{$name}/{$this->stem}.webp",
                    (string) $encoded,
                );
            }

            $this->deleteOldVariants();
            $this->profile->update(['avatar_path' => $this->stem]);

            broadcast(new AvatarProcessed($this->profile->fresh()));
        } finally {
            Storage::disk('local')->delete($this->tmpPath);
        }
    }

    private function deleteOldVariants(): void
    {
        $old = $this->profile->avatar_path;

        if (! $old) {
            return;
        }

        $config = config('avatars');

        foreach (array_keys($config['variants']) as $name) {
            Storage::disk($config['disk'])->delete("{$config['base_dir']}/{$name}/{$old}.webp");
        }
    }
}
