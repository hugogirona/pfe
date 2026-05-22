<?php

use App\Actions\UploadMediaImage;
use App\Enums\MediaType;
use App\Jobs\ProcessMediaImage;
use App\Models\Media;
use App\Models\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    Storage::fake('public');
});

it('creates a Media row immediately with processed_at null', function () {
    Bus::fake();
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
        ->height->toBe(800)
        ->and($media->processed_at)->toBeNull();
});

it('queues ProcessMediaImage instead of running it synchronously', function () {
    Bus::fake();
    $profile = Profile::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg', 1200, 800);

    $media = app(UploadMediaImage::class)->execute($profile, $file);

    Bus::assertDispatched(ProcessMediaImage::class, function (ProcessMediaImage $job) use ($media) {
        return $job->media->id === $media->id
            && str_starts_with($job->tmpPath, 'media-tmp/')
            && $job->stem === $media->source
            && $job->replacedSource === null;
    });
});

it('does not generate variants synchronously', function () {
    Bus::fake();
    $profile = Profile::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg', 1200, 800);

    app(UploadMediaImage::class)->execute($profile, $file);

    expect(Storage::disk('public')->files('media/thumbnail'))->toBeEmpty()
        ->and(Storage::disk('public')->files('media/medium'))->toBeEmpty();
});

it('stores the original to the tmp directory', function () {
    Bus::fake();
    $profile = Profile::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg', 1200, 800);

    app(UploadMediaImage::class)->execute($profile, $file);

    expect(Storage::disk('local')->files('media-tmp'))->toHaveCount(1);
});

it('assigns the next position (max + 1)', function () {
    Bus::fake();
    $profile = Profile::factory()->create();
    Media::factory()->create(['profile_id' => $profile->id, 'position' => 0]);
    Media::factory()->create(['profile_id' => $profile->id, 'position' => 4]);

    $file = UploadedFile::fake()->image('photo.jpg', 800, 600);
    $media = app(UploadMediaImage::class)->execute($profile, $file);

    expect($media->position)->toBe(5);
});

it('handles null caption', function () {
    Bus::fake();
    $profile = Profile::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg');

    $media = app(UploadMediaImage::class)->execute($profile, $file);

    expect($media->caption)->toBeNull();
});

it('throws if image dimensions cannot be read', function () {
    Bus::fake();
    $profile = Profile::factory()->create();
    $file = UploadedFile::fake()->create('photo.jpg', 10, 'image/jpeg');

    expect(fn () => app(UploadMediaImage::class)->execute($profile, $file))
        ->toThrow(RuntimeException::class);
});

it('assigns sequential positions without duplicates under repeated upload', function () {
    Bus::fake();
    $profile = Profile::factory()->create();
    $action = app(UploadMediaImage::class);

    foreach (range(1, 5) as $i) {
        $action->execute($profile, UploadedFile::fake()->image("photo-{$i}.jpg"));
    }

    expect($profile->media()->pluck('position')->all())->toBe([0, 1, 2, 3, 4]);
});

it('refuses to replace a non-image media', function () {
    Bus::fake();
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

it('replace marks the media as processing and queues the job with the old source', function () {
    Bus::fake();
    $profile = Profile::factory()->create();
    $media = Media::factory()->image()->create([
        'profile_id' => $profile->id,
        'source' => 'gallery-photo-old',
        'position' => 0,
    ]);

    app(UploadMediaImage::class)->replace($media, UploadedFile::fake()->image('new.jpg', 1600, 900));

    $media->refresh();
    expect($media)
        ->source->toBe('gallery-photo-old')
        ->width->toBe(1600)
        ->height->toBe(900)
        ->and($media->processed_at)->toBeNull();

    Bus::assertDispatched(ProcessMediaImage::class, function (ProcessMediaImage $job) use ($media) {
        return $job->media->id === $media->id
            && $job->replacedSource === 'gallery-photo-old'
            && $job->stem !== 'gallery-photo-old';
    });
});
