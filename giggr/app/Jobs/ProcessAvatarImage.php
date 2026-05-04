<?php

namespace App\Jobs;

use App\Models\Profile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class ProcessAvatarImage implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Profile $profile,
        private readonly string $tmpPath,
        private readonly string $stem,
    ) {}

    public function handle(): void
    {
        $manager = ImageManager::usingDriver(GdDriver::class);
        $absolutePath = Storage::disk('local')->path($this->tmpPath);
        $config = config('avatars');

        try {
            foreach ($config['variants'] as $name => $size) {
                $encoded = $manager
                    ->decodePath($absolutePath)
                    ->cover($size['width'], $size['height'])
                    ->encode(new WebpEncoder(quality: $config['quality']));

                Storage::disk($config['disk'])->put(
                    "avatars/{$name}/{$this->stem}.webp",
                    (string) $encoded,
                );
            }

            $this->deleteOldVariants();
            $this->profile->update(['avatar_path' => $this->stem]);
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
            Storage::disk($config['disk'])->delete("avatars/{$name}/{$old}.webp");
        }
    }
}
