<?php

use App\Events\MediaProcessed;
use App\Jobs\ProcessMediaImage;
use App\Models\Media;
use App\Models\Profile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

function sampleMediaJpeg(int $width = 1200, int $height = 800): string
{
    $image = imagecreatetruecolor($width, $height);
    ob_start();
    imagejpeg($image);

    return (string) ob_get_clean();
}

function pendingMedia(string $stem, ?int $width = 1200, ?int $height = 800): Media
{
    return Media::factory()->processing()->create([
        'profile_id' => Profile::factory()->create()->id,
        'source' => $stem,
        'width' => $width,
        'height' => $height,
        'position' => 0,
    ]);
}

it('creates thumbnail and medium webp variants on the public disk', function () {
    Storage::fake('local');
    Storage::fake('public');
    Storage::disk('local')->put('media-tmp/original.jpg', sampleMediaJpeg());
    $media = pendingMedia('my-stem');

    ProcessMediaImage::dispatchSync($media, 'media-tmp/original.jpg', 'my-stem');

    Storage::disk('public')->assertExists('media/thumbnail/my-stem.webp')
        ->assertExists('media/medium/my-stem.webp');
});

it('deletes the original temp file after processing', function () {
    Storage::fake('local');
    Storage::fake('public');
    Storage::disk('local')->put('media-tmp/original.jpg', sampleMediaJpeg());
    $media = pendingMedia('my-stem');

    ProcessMediaImage::dispatchSync($media, 'media-tmp/original.jpg', 'my-stem');

    Storage::disk('local')->assertMissing('media-tmp/original.jpg');
});

it('sets processed_at on the media after a successful run', function () {
    Storage::fake('local');
    Storage::fake('public');
    Storage::disk('local')->put('media-tmp/original.jpg', sampleMediaJpeg());
    $media = pendingMedia('my-stem');

    ProcessMediaImage::dispatchSync($media, 'media-tmp/original.jpg', 'my-stem');

    expect($media->fresh()->processed_at)->not->toBeNull();
});

it('swaps source and deletes old variants on replace', function () {
    Storage::fake('local');
    Storage::fake('public');
    Storage::disk('local')->put('media-tmp/new.jpg', sampleMediaJpeg());
    Storage::disk('public')->put('media/thumbnail/old-stem.webp', 'fake');
    Storage::disk('public')->put('media/medium/old-stem.webp', 'fake');
    $media = pendingMedia('old-stem');

    ProcessMediaImage::dispatchSync($media, 'media-tmp/new.jpg', 'new-stem', 'old-stem');

    expect($media->fresh()->source)->toBe('new-stem');
    Storage::disk('public')->assertMissing('media/thumbnail/old-stem.webp')
        ->assertMissing('media/medium/old-stem.webp')
        ->assertExists('media/thumbnail/new-stem.webp')
        ->assertExists('media/medium/new-stem.webp');
});

it('broadcasts MediaProcessed after a successful run', function () {
    Storage::fake('local');
    Storage::fake('public');
    Event::fake([MediaProcessed::class]);
    Storage::disk('local')->put('media-tmp/original.jpg', sampleMediaJpeg());
    $media = pendingMedia('my-stem');

    ProcessMediaImage::dispatchSync($media, 'media-tmp/original.jpg', 'my-stem');

    Event::assertDispatched(
        MediaProcessed::class,
        fn (MediaProcessed $e) => $e->media->id === $media->id
            && $e->media->processed_at !== null,
    );
});

it('does not broadcast MediaProcessed if encoding fails', function () {
    Storage::fake('local');
    Storage::fake('public');
    Event::fake([MediaProcessed::class]);
    Storage::disk('local')->put('media-tmp/bad.jpg', 'not-an-image');
    $media = pendingMedia('bad-stem');

    try {
        ProcessMediaImage::dispatchSync($media, 'media-tmp/bad.jpg', 'bad-stem');
    } catch (Throwable) {
        // expected
    }

    Event::assertNotDispatched(MediaProcessed::class);
});

it('preserves aspect ratio when scaling down (no center-crop)', function () {
    Storage::fake('local');
    Storage::fake('public');
    Storage::disk('local')->put('media-tmp/wide.jpg', sampleMediaJpeg(2000, 1000));
    $media = pendingMedia('wide-stem');

    ProcessMediaImage::dispatchSync($media, 'media-tmp/wide.jpg', 'wide-stem');

    $thumbnail = Image::decodePath(Storage::disk('public')->path('media/thumbnail/wide-stem.webp'));

    expect($thumbnail->width())->toBe(400)
        ->and($thumbnail->height())->toBe(200);
});

it('does not upscale a small source image', function () {
    Storage::fake('local');
    Storage::fake('public');
    Storage::disk('local')->put('media-tmp/small.jpg', sampleMediaJpeg(300, 200));
    $media = pendingMedia('small-stem');

    ProcessMediaImage::dispatchSync($media, 'media-tmp/small.jpg', 'small-stem');

    $thumbnail = Image::decodePath(Storage::disk('public')->path('media/thumbnail/small-stem.webp'));

    expect($thumbnail->width())->toBe(300)
        ->and($thumbnail->height())->toBe(200);
});

it('still deletes the temp file even if encoding fails', function () {
    Storage::fake('local');
    Storage::fake('public');
    Storage::disk('local')->put('media-tmp/bad.jpg', 'not-an-image');
    $media = pendingMedia('bad-stem');

    try {
        ProcessMediaImage::dispatchSync($media, 'media-tmp/bad.jpg', 'bad-stem');
    } catch (Throwable) {
        // expected
    }

    Storage::disk('local')->assertMissing('media-tmp/bad.jpg');
});
