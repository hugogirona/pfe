<?php

use App\Actions\UploadAvatarImage;
use App\Jobs\ProcessAvatarImage;
use App\Models\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    Storage::fake('public');
});

it('stores the original to the tmp directory', function () {
    Bus::fake();
    $profile = Profile::factory()->create(['avatar_path' => null]);

    app(UploadAvatarImage::class)->execute($profile, UploadedFile::fake()->image('avatar.jpg', 400, 400));

    expect(Storage::disk('local')->files('avatars-tmp'))->toHaveCount(1);
});

it('queues ProcessAvatarImage instead of running it synchronously', function () {
    Bus::fake();
    $profile = Profile::factory()->create(['avatar_path' => null]);

    app(UploadAvatarImage::class)->execute($profile, UploadedFile::fake()->image('avatar.jpg', 400, 400));

    Bus::assertDispatched(ProcessAvatarImage::class, function (ProcessAvatarImage $job) use ($profile) {
        return $job->profile->id === $profile->id
            && str_starts_with($job->tmpPath, 'avatars-tmp/')
            && $job->stem !== '';
    });
});

it('does not generate variants synchronously', function () {
    Bus::fake();
    $profile = Profile::factory()->create(['avatar_path' => null]);

    app(UploadAvatarImage::class)->execute($profile, UploadedFile::fake()->image('avatar.jpg', 400, 400));

    expect(Storage::disk('public')->files('avatars/thumbnail'))->toBeEmpty()
        ->and(Storage::disk('public')->files('avatars/medium'))->toBeEmpty();
});

it('does not update avatar_path synchronously', function () {
    Bus::fake();
    $profile = Profile::factory()->create(['avatar_path' => null]);

    app(UploadAvatarImage::class)->execute($profile, UploadedFile::fake()->image('avatar.jpg', 400, 400));

    expect($profile->fresh()->avatar_path)->toBeNull();
});
