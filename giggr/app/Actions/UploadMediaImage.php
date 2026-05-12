<?php

namespace App\Actions;

use App\Enums\MediaType;
use App\Jobs\ProcessMediaImage;
use App\Models\Media;
use App\Models\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class UploadMediaImage
{
    private const string TMP_DIR = 'media-tmp';

    public function execute(Profile $profile, UploadedFile $file, ?string $caption = null): Media
    {
        $dimensions = @getimagesize($file->getRealPath());
        $width = $dimensions[0] ?? null;
        $height = $dimensions[1] ?? null;

        $stem = 'gallery-photo-'.Str::random(12);
        $tmpPath = $file->storeAs(
            self::TMP_DIR,
            $stem.'.'.$file->getClientOriginalExtension(),
            'local',
        );

        ProcessMediaImage::dispatchSync($tmpPath, $stem);

        return Media::create([
            'profile_id' => $profile->id,
            'type' => MediaType::Image,
            'source' => $stem,
            'caption' => $caption,
            'position' => ($profile->media()->max('position') ?? -1) + 1,
            'width' => $width,
            'height' => $height,
        ]);
    }
}
