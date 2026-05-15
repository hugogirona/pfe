<?php

use App\Actions\UploadMediaImage;
use App\Enums\MediaType;
use App\Models\Media;
use App\Models\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    Storage::fake('public');
});

it('creates a Media row and generates variants for a profile', function () {
    $profile = Profile::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg', 1200, 800);

    $media = app(UploadMediaImage::class)->execute($profile, $file, 'Mon concert');

    expect($media)
        ->profile_id->toBe($profile->id)
        ->type->toBe(MediaType::Image)
        ->source->toStartWith('gallery-photo-')
        ->caption->toBe('Mon concert')
        ->position->toBe(0)
        ->width->toBe(1200)
        ->height->toBe(800);

    Storage::disk('public')
        ->assertExists("media/thumbnail/{$media->source}.webp")
        ->assertExists("media/medium/{$media->source}.webp");
});

it('assigns the next position (max + 1)', function () {
    $profile = Profile::factory()->create();
    Media::factory()->create(['profile_id' => $profile->id, 'position' => 0]);
    Media::factory()->create(['profile_id' => $profile->id, 'position' => 4]);

    $file = UploadedFile::fake()->image('photo.jpg', 800, 600);
    $media = app(UploadMediaImage::class)->execute($profile, $file);

    expect($media->position)->toBe(5);
});

it('handles null caption', function () {
    $profile = Profile::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg');

    $media = app(UploadMediaImage::class)->execute($profile, $file);

    expect($media->caption)->toBeNull();
});

it('throws if image dimensions cannot be read', function () {
    $profile = Profile::factory()->create();
    $file = UploadedFile::fake()->create('photo.jpg', 10, 'image/jpeg');

    expect(fn () => app(UploadMediaImage::class)->execute($profile, $file))
        ->toThrow(RuntimeException::class);
});

it('assigns sequential positions without duplicates under repeated upload', function () {
    $profile = Profile::factory()->create();
    $action = app(UploadMediaImage::class);

    foreach (range(1, 5) as $i) {
        $action->execute($profile, UploadedFile::fake()->image("photo-{$i}.jpg"));
    }

    expect($profile->media()->pluck('position')->all())->toBe([0, 1, 2, 3, 4]);
});

it('refuses to replace a non-image media', function () {
    $profile = Profile::factory()->create();
    $media = Media::factory()->youtube()->create([
        'profile_id' => $profile->id,
        'position' => 0,
    ]);
    $originalSource = $media->source;

    expect(fn () => app(UploadMediaImage::class)->replace($media, UploadedFile::fake()->image('photo.jpg')))
        ->toThrow(InvalidArgumentException::class)
        ->and($media->fresh())
        ->source->toBe($originalSource)
        ->type->toBe(MediaType::Youtube);

});
