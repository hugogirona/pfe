<?php

namespace App\Actions;

use App\Enums\MediaType;
use App\Jobs\ProcessMediaImage;
use App\Models\Media;
use App\Models\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class UploadMediaImage
{
    private const string TMP_DIR = 'media-tmp';

    public function execute(Profile $profile, UploadedFile $file, ?string $caption = null): Media
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
}
