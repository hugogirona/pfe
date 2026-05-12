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
