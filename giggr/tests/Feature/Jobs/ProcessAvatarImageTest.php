<?php

use App\Events\AvatarProcessed;
use App\Jobs\ProcessAvatarImage;
use App\Models\Profile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

function sampleJpegContent(): string
{
    $image = imagecreatetruecolor(400, 400);
    ob_start();
    imagejpeg($image);

    return (string) ob_get_clean();
}

it('creates thumbnail and medium webp variants on the public disk', function () {
    Storage::fake('local');
    Storage::fake('public');

    $profile = Profile::factory()->create(['avatar_path' => null]);
    Storage::disk('local')->put('avatars-tmp/original.jpg', sampleJpegContent());

    ProcessAvatarImage::dispatchSync($profile, 'avatars-tmp/original.jpg', 'my-stem');

    Storage::disk('public')->assertExists('avatars/thumbnail/my-stem.webp');
    Storage::disk('public')->assertExists('avatars/medium/my-stem.webp');
});

it('updates avatar_path on the profile after processing', function () {
    Storage::fake('local');
    Storage::fake('public');

    $profile = Profile::factory()->create(['avatar_path' => null]);
    Storage::disk('local')->put('avatars-tmp/original.jpg', sampleJpegContent());

    ProcessAvatarImage::dispatchSync($profile, 'avatars-tmp/original.jpg', 'my-stem');

    expect($profile->fresh()->avatar_path)->toBe('my-stem');
});

it('deletes the original temp file after processing', function () {
    Storage::fake('local');
    Storage::fake('public');

    $profile = Profile::factory()->create(['avatar_path' => null]);
    Storage::disk('local')->put('avatars-tmp/original.jpg', sampleJpegContent());

    ProcessAvatarImage::dispatchSync($profile, 'avatars-tmp/original.jpg', 'my-stem');

    Storage::disk('local')->assertMissing('avatars-tmp/original.jpg');
});

it('overwrites existing avatar variants when a new upload is processed', function () {
    Storage::fake('local');
    Storage::fake('public');

    $profile = Profile::factory()->create(['avatar_path' => 'old-stem']);
    Storage::disk('public')->put('avatars/thumbnail/old-stem.webp', 'fake');
    Storage::disk('public')->put('avatars/medium/old-stem.webp', 'fake');
    Storage::disk('local')->put('avatars-tmp/original.jpg', sampleJpegContent());

    ProcessAvatarImage::dispatchSync($profile, 'avatars-tmp/original.jpg', 'new-stem');

    Storage::disk('public')->assertMissing('avatars/thumbnail/old-stem.webp')
        ->assertMissing('avatars/medium/old-stem.webp')
        ->assertExists('avatars/thumbnail/new-stem.webp')
        ->assertExists('avatars/medium/new-stem.webp');
});

it('broadcasts AvatarProcessed after a successful run', function () {
    Storage::fake('local');
    Storage::fake('public');
    Event::fake([AvatarProcessed::class]);

    $profile = Profile::factory()->create(['avatar_path' => null]);
    Storage::disk('local')->put('avatars-tmp/original.jpg', sampleJpegContent());

    ProcessAvatarImage::dispatchSync($profile, 'avatars-tmp/original.jpg', 'my-stem');

    Event::assertDispatched(
        AvatarProcessed::class,
        fn (AvatarProcessed $e) => $e->profile->id === $profile->id
            && $e->profile->avatar_path === 'my-stem',
    );
});

it('does not broadcast AvatarProcessed if encoding fails', function () {
    Storage::fake('local');
    Storage::fake('public');
    Event::fake([AvatarProcessed::class]);

    $profile = Profile::factory()->create(['avatar_path' => null]);
    Storage::disk('local')->put('avatars-tmp/bad.jpg', 'not-an-image');

    try {
        ProcessAvatarImage::dispatchSync($profile, 'avatars-tmp/bad.jpg', 'bad-stem');
    } catch (Throwable) {
        // expected
    }

    Event::assertNotDispatched(AvatarProcessed::class);
});
