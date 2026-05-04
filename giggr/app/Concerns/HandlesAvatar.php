<?php

namespace App\Concerns;

use App\Jobs\ProcessAvatarImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandlesAvatar
{
    public function processAvatar(UploadedFile $file): void
    {
        $stem = Str::slug($this->user->full_name).'-'.Str::random(8);
        $tmpPath = $file->storeAs('avatars-tmp', $stem.'.'.$file->getClientOriginalExtension(), 'local');

        ProcessAvatarImage::dispatchSync($this, $tmpPath, $stem);
    }

    public function avatarVariantUrl(string $variant): ?string
    {
        if (! $this->avatar_path) {
            return null;
        }

        return Storage::disk(config('avatars.disk'))->url("avatars/{$variant}/{$this->avatar_path}.webp");
    }
}
