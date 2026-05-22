<?php

namespace App\Actions;

use App\Jobs\ProcessAvatarImage;
use App\Models\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class UploadAvatarImage
{
    private const string TMP_DIR = 'avatars-tmp';

    public function execute(Profile $profile, UploadedFile $file): void
    {
        $stem = Str::slug($profile->user->full_name).'-'.Str::random(8);
        $tmpPath = $file->storeAs(
            self::TMP_DIR,
            $stem.'.'.$file->getClientOriginalExtension(),
            'local',
        );

        ProcessAvatarImage::dispatch($profile, $tmpPath, $stem);
    }
}
