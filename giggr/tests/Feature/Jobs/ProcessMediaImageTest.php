<?php

use App\Jobs\ProcessMediaImage;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

function sampleMediaJpeg(int $width = 1200, int $height = 800): string
{
    $image = imagecreatetruecolor($width, $height);
    ob_start();
    imagejpeg($image);

    return (string) ob_get_clean();
}

it('creates thumbnail and medium webp variants on the public disk', function () {
    Storage::fake('local');
    Storage::fake('public');

    Storage::disk('local')->put('media-tmp/original.jpg', sampleMediaJpeg());

    ProcessMediaImage::dispatchSync('media-tmp/original.jpg', 'my-stem');

    Storage::disk('public')->assertExists('media/thumbnail/my-stem.webp')
        ->assertExists('media/medium/my-stem.webp');
});

it('deletes the original temp file after processing', function () {
    Storage::fake('local');
    Storage::fake('public');

    Storage::disk('local')->put('media-tmp/original.jpg', sampleMediaJpeg());

    ProcessMediaImage::dispatchSync('media-tmp/original.jpg', 'my-stem');

    Storage::disk('local')->assertMissing('media-tmp/original.jpg');
});

it('preserves aspect ratio when scaling down (no center-crop)', function () {
    Storage::fake('local');
    Storage::fake('public');

    // 2000x1000 = 2:1 ratio. After scaleDown to max-edge 400, expect 400x200.
    Storage::disk('local')->put('media-tmp/wide.jpg', sampleMediaJpeg(2000, 1000));

    ProcessMediaImage::dispatchSync('media-tmp/wide.jpg', 'wide-stem');

    $thumbnail = Image::decodePath(Storage::disk('public')->path('media/thumbnail/wide-stem.webp'));

    expect($thumbnail->width())->toBe(400)
        ->and($thumbnail->height())->toBe(200);
});

it('does not upscale a small source image', function () {
    Storage::fake('local');
    Storage::fake('public');

    // 300x200 is smaller than every variant's max-edge → keep original size
    Storage::disk('local')->put('media-tmp/small.jpg', sampleMediaJpeg(300, 200));

    ProcessMediaImage::dispatchSync('media-tmp/small.jpg', 'small-stem');

    $thumbnail = Image::decodePath(Storage::disk('public')->path('media/thumbnail/small-stem.webp'));

    expect($thumbnail->width())->toBe(300)
        ->and($thumbnail->height())->toBe(200);
});

it('still deletes the temp file even if encoding fails', function () {
    Storage::fake('local');
    Storage::fake('public');

    // A garbage tmp file that Intervention cannot decode
    Storage::disk('local')->put('media-tmp/bad.jpg', 'not-an-image');

    try {
        ProcessMediaImage::dispatchSync('media-tmp/bad.jpg', 'bad-stem');
    } catch (Throwable) {
        // expected
    }

    Storage::disk('local')->assertMissing('media-tmp/bad.jpg');
});
