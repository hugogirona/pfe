<?php

namespace App\Actions;

use App\Enums\MediaType;
use App\Jobs\ProcessMediaImage;
use App\Models\Media;
use App\Models\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class UploadMediaImage
{
    private const string TMP_DIR = 'media-tmp';

    /**
     * @throws Throwable
     */
    public function execute(Profile $profile, UploadedFile $file, ?string $caption = null): Media
    {
        [$stem, $width, $height] = $this->processVariants($file);

        return DB::transaction(function () use ($profile, $stem, $caption, $width, $height): Media {
            $lockedProfile = Profile::query()->lockForUpdate()->findOrFail($profile->id);
            $nextPosition = ($lockedProfile->media()->max('position') ?? -1) + 1;

            return Media::create([
                'profile_id' => $lockedProfile->id,
                'type' => MediaType::Image,
                'source' => $stem,
                'caption' => $caption,
                'position' => $nextPosition,
                'width' => $width,
                'height' => $height,
            ]);
        });
    }

    public function replace(Media $media, UploadedFile $file): void
    {
        if ($media->type !== MediaType::Image) {
            throw new InvalidArgumentException(
                "Cannot replace a non-image media (id={$media->id}, type={$media->type->value}).",
            );
        }

        [$newStem, $width, $height] = $this->processVariants($file);

        $oldSource = $media->source;

        $media->update([
            'source' => $newStem,
            'width' => $width,
            'height' => $height,
        ]);

        Media::deleteVariantsForSource($oldSource);
    }

    /**
     * @return array{0: string, 1: int, 2: int}
     */
    private function processVariants(UploadedFile $file): array
    {
        $dimensions = @getimagesize($file->getRealPath());
        if ($dimensions === false) {
            throw new RuntimeException(
                "Could not read image dimensions from upload '{$file->getClientOriginalName()}'.",
            );
        }
        [$width, $height] = $dimensions;

        $stem = 'gallery-photo-'.Str::random(12);
        $tmpPath = $file->storeAs(
            self::TMP_DIR,
            $stem.'.'.$file->getClientOriginalExtension(),
            'local',
        );

        ProcessMediaImage::dispatchSync($tmpPath, $stem);

        return [$stem, $width, $height];
    }
}
