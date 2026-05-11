<?php

namespace Tests\Feature\Models;

use App\Enums\MediaType;
use App\Models\Media;
use App\Models\Profile;
use Illuminate\Database\QueryException;

it('can create an image media with all fields', function () {
    $profile = Profile::factory()->create();

    $media = Media::create([
        'profile_id' => $profile->id,
        'type' => MediaType::Image,
        'source' => 'profiles-photo-abc12345',
        'position' => 0,
        'caption' => 'Mon dernier concert',
        'width' => 1920,
        'height' => 1280,
    ]);

    expect($media->fresh())
        ->profile_id->toBe($profile->id)
        ->type->toBe(MediaType::Image)
        ->source->toBe('profiles-photo-abc12345')
        ->position->toBe(0)
        ->caption->toBe('Mon dernier concert')
        ->width->toBe(1920)
        ->height->toBe(1280);
});

it('can create a youtube media without dimensions', function () {
    $profile = Profile::factory()->create();

    $media = Media::create([
        'profile_id' => $profile->id,
        'type' => MediaType::Youtube,
        'source' => 'cj9kbTU9pKA',
        'position' => 0,
    ]);

    expect($media->fresh())
        ->type->toBe(MediaType::Youtube)
        ->source->toBe('cj9kbTU9pKA')
        ->width->toBeNull()
        ->height->toBeNull()
        ->caption->toBeNull();
});

it('casts type to the MediaType enum', function () {
    $profile = Profile::factory()->create();
    $media = Media::create([
        'profile_id' => $profile->id,
        'type' => 'youtube',
        'source' => 'cj9kbTU9pKA',
    ]);

    expect($media->fresh()->type)->toBeInstanceOf(MediaType::class)
        ->and($media->fresh()->type)->toBe(MediaType::Youtube);
});

it('belongs to a profile', function () {
    $profile = Profile::factory()->create();
    $media = Media::factory()->create(['profile_id' => $profile->id]);

    expect($media->profile)->toBeInstanceOf(Profile::class)
        ->and($media->profile->id)->toBe($profile->id);
});

it('cascades on profile delete', function () {
    $profile = Profile::factory()->create();
    Media::factory()
        ->count(3)
        ->sequence(fn ($s) => ['position' => $s->index])
        ->create(['profile_id' => $profile->id]);

    $profile->forceDelete();

    expect(Media::count())->toBe(0);
});

it('exposes a working factory with image state', function () {
    $media = Media::factory()->image()->create();

    expect($media)->toBeInstanceOf(Media::class)
        ->and($media->type)->toBe(MediaType::Image)
        ->and($media->source)->not->toBeEmpty()
        ->and($media->width)->toBeInt()
        ->and($media->height)->toBeInt();
});

it('exposes a working factory with youtube state', function () {
    $media = Media::factory()->youtube()->create();

    expect($media)->toBeInstanceOf(Media::class)
        ->and($media->type)->toBe(MediaType::Youtube)
        ->and($media->source)->toMatch('/^[A-Za-z0-9_-]{11}$/')
        ->and($media->width)->toBeNull()
        ->and($media->height)->toBeNull();
});

it('Profile::media() returns medias ordered by position', function () {
    $profile = Profile::factory()->create();

    Media::factory()->create(['profile_id' => $profile->id, 'position' => 2]);
    Media::factory()->create(['profile_id' => $profile->id, 'position' => 0]);
    Media::factory()->create(['profile_id' => $profile->id, 'position' => 1]);

    expect($profile->media->pluck('position')->all())->toBe([0, 1, 2]);
});

it('display_url accessor returns the youtube embed url for youtube type', function () {
    $media = Media::factory()->youtube()->create(['source' => 'cj9kbTU9pKA']);

    expect($media->display_url)->toBe('https://www.youtube.com/embed/cj9kbTU9pKA');
});

it('display_url accessor returns a Storage url for image type', function () {
    $media = Media::factory()->image()->create(['source' => 'gallery-photo-abc12345']);
    expect($media->display_url)->toContain('gallery-photo-abc12345');
});

it('youtube_thumbnail_url accessor returns the i.ytimg.com hqdefault url', function () {
    $media = Media::factory()->youtube()->create(['source' => 'cj9kbTU9pKA']);

    expect($media->youtube_thumbnail_url)->toBe('https://i.ytimg.com/vi/cj9kbTU9pKA/hqdefault.jpg');
});

it('youtube_thumbnail_url is null for image type', function () {
    $media = Media::factory()->image()->create();

    expect($media->youtube_thumbnail_url)->toBeNull();
});

it('profile_id is required (not null at the DB level)', function () {
    expect(fn () => Media::create([
        'type' => MediaType::Image,
        'source' => 'whatever',
    ]))->toThrow(QueryException::class);
});

it('enforces unique (profile_id, position) at the DB level', function () {
    $profile = Profile::factory()->create();
    Media::factory()->create(['profile_id' => $profile->id, 'position' => 5]);

    expect(fn () => Media::factory()->create([
        'profile_id' => $profile->id,
        'position' => 5,
    ]))->toThrow(QueryException::class);
});

it('allows the same position across different profiles', function () {
    $profile1 = Profile::factory()->create();
    $profile2 = Profile::factory()->create();

    Media::factory()->create(['profile_id' => $profile1->id, 'position' => 0]);
    Media::factory()->create(['profile_id' => $profile2->id, 'position' => 0]);

    expect(Media::count())->toBe(2);
});
