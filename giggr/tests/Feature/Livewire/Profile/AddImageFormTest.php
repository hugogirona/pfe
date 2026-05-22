<?php

use App\Enums\MediaType;
use App\Jobs\ProcessMediaImage;
use App\Models\Media;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    Bus::fake();
    Storage::fake('local');
    Storage::fake('public');
});

it('owner can upload a JPEG image with a caption', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-image-form', ['model_id' => $profile->id])
        ->set('photo', UploadedFile::fake()->image('photo.jpg', 1200, 800))
        ->set('caption', 'Mon dernier concert')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('media-added')
        ->assertNotDispatched('close-modal')
        ->assertSet('success', true);

    expect(Media::count())->toBe(1)
        ->and(Media::first())
        ->type->toBe(MediaType::Image)
        ->caption->toBe('Mon dernier concert')
        ->width->toBe(1200)
        ->height->toBe(800)
        ->profile_id->toBe($profile->id);
});

it('queues variant generation rather than running it synchronously', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-image-form', ['model_id' => $profile->id])
        ->set('photo', UploadedFile::fake()->image('photo.jpg', 800, 600))
        ->call('save');

    Bus::assertDispatched(ProcessMediaImage::class);
    expect(Media::first()->processed_at)->toBeNull();
});

it('assigns the next position automatically (max + 1)', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);
    Media::factory()->create(['profile_id' => $profile->id, 'position' => 0]);
    Media::factory()->create(['profile_id' => $profile->id, 'position' => 5]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-image-form', ['model_id' => $profile->id])
        ->set('photo', UploadedFile::fake()->image('photo.jpg'))
        ->call('save')
        ->assertHasNoErrors();

    expect(Media::orderByDesc('position')->first()->position)->toBe(6);
});

it('rejects a non-image file', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-image-form', ['model_id' => $profile->id])
        ->set('photo', UploadedFile::fake()->create('doc.pdf', 100))
        ->call('save')
        ->assertHasErrors(['photo']);

    expect(Media::count())->toBe(0);
});

it('rejects a missing photo', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-image-form', ['model_id' => $profile->id])
        ->call('save')
        ->assertHasErrors(['photo' => 'required']);
});

it('rejects a file larger than the cap', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-image-form', ['model_id' => $profile->id])
        // 6MB > 5MB cap (config('media.max_file_size') = 5 * 1024 KB)
        ->set('photo', UploadedFile::fake()->image('big.jpg')->size(6 * 1024))
        ->call('save')
        ->assertHasErrors(['photo']);

    expect(Media::count())->toBe(0);
});

it('caption is optional', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-image-form', ['model_id' => $profile->id])
        ->set('photo', UploadedFile::fake()->image('photo.jpg'))
        ->call('save')
        ->assertHasNoErrors();

    expect(Media::first()->caption)->toBeNull();
});

it('caption length is capped', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-image-form', ['model_id' => $profile->id])
        ->set('photo', UploadedFile::fake()->image('photo.jpg'))
        ->set('caption', str_repeat('a', 256))
        ->call('save')
        ->assertHasErrors(['caption']);
});

it('rejects when the profile already has 20 medias', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);
    Media::factory()
        ->count(20)
        ->sequence(fn ($s) => ['position' => $s->index])
        ->create(['profile_id' => $profile->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-image-form', ['model_id' => $profile->id])
        ->set('photo', UploadedFile::fake()->image('photo.jpg'))
        ->call('save')
        ->assertHasErrors(['photo']);

    expect(Media::count())->toBe(20);
});

it('non-owner cannot upload', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);
    $stranger = User::factory()->create();

    Livewire::actingAs($stranger)
        ->test('parts.profile.add-image-form', ['model_id' => $profile->id])
        ->set('photo', UploadedFile::fake()->image('photo.jpg'))
        ->call('save')
        ->assertForbidden();

    expect(Media::count())->toBe(0);
});

it('guest cannot upload', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::test('parts.profile.add-image-form', ['model_id' => $profile->id])
        ->set('photo', UploadedFile::fake()->image('photo.jpg'))
        ->call('save')
        ->assertForbidden();

    expect(Media::count())->toBe(0);
});

it('close action dispatches close-modal', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-image-form', ['model_id' => $profile->id])
        ->set('photo', UploadedFile::fake()->image('photo.jpg'))
        ->call('save')
        ->call('close')
        ->assertDispatched('close-modal');
});

it('shows the success message after save', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-image-form', ['model_id' => $profile->id])
        ->set('photo', UploadedFile::fake()->image('photo.jpg'))
        ->call('save')
        ->assertSee(__('profile.add_image_success_title'))
        ->assertSee(__('profile.add_image_success_body'))
        ->assertSee(__('profile.add_image_close'));
});

// -- Edit mode --------------------------------------------------------------

it('prefills the caption when mounted in edit mode', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);
    $media = Media::factory()->image()->create([
        'profile_id' => $profile->id,
        'caption' => 'Caption d\'origine',
        'position' => 0,
    ]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-image-form', ['media_id' => $media->id])
        ->assertSet('caption', 'Caption d\'origine')
        ->assertSet('isEdit', true);
});

it('updates only the caption when no new photo is provided', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);
    $media = Media::factory()->image()->create([
        'profile_id' => $profile->id,
        'source' => 'gallery-photo-untouched',
        'caption' => 'Avant',
        'width' => 1000,
        'height' => 800,
        'position' => 0,
    ]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-image-form', ['media_id' => $media->id])
        ->set('caption', 'Après')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('media-updated')
        ->assertSet('success', true);

    $media->refresh();
    expect($media)
        ->caption->toBe('Après')
        ->source->toBe('gallery-photo-untouched')
        ->width->toBe(1000)
        ->and($media->height)->toBe(800);
});

it('replaces the image by queueing a job with the old source for cleanup', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);
    $media = Media::factory()->image()->create([
        'profile_id' => $profile->id,
        'source' => 'gallery-photo-old',
        'caption' => 'Avant',
        'position' => 0,
    ]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-image-form', ['media_id' => $media->id])
        ->set('photo', UploadedFile::fake()->image('new.jpg', 1600, 900))
        ->set('caption', 'Après')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('media-updated');

    $media->refresh();
    expect($media)
        ->caption->toBe('Après')
        ->source->toBe('gallery-photo-old')
        ->width->toBe(1600)
        ->and($media->height)->toBe(900)
        ->and($media->processed_at)->toBeNull();

    Bus::assertDispatched(
        ProcessMediaImage::class,
        fn (ProcessMediaImage $job) => $job->media->id === $media->id
            && $job->replacedSource === 'gallery-photo-old',
    );
});

it('skips the cap check in edit mode (already counted)', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);
    $medias = Media::factory()
        ->count(20)
        ->sequence(fn ($s) => ['position' => $s->index])
        ->create(['profile_id' => $profile->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-image-form', ['media_id' => $medias->first()->id])
        ->set('caption', 'Edition autorisée')
        ->call('save')
        ->assertHasNoErrors();
});

it('non-owner cannot edit a media', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);
    $media = Media::factory()->image()->create(['profile_id' => $profile->id, 'position' => 0]);
    $stranger = User::factory()->create();

    Livewire::actingAs($stranger)
        ->test('parts.profile.add-image-form', ['media_id' => $media->id])
        ->set('caption', 'Hack')
        ->call('save')
        ->assertForbidden();

    expect($media->fresh()->caption)->not->toBe('Hack');
});

it('shows the update success message after edit', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);
    $media = Media::factory()->image()->create(['profile_id' => $profile->id, 'position' => 0]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-image-form', ['media_id' => $media->id])
        ->set('caption', 'Nouveau')
        ->call('save')
        ->assertSee(__('profile.update_image_success_title'));
});

// -- Delete mode ------------------------------------------------------------

it('deletes the media and its variants from disk', function () {
    Storage::fake('local');
    Storage::fake('public');

    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);
    $media = Media::factory()->image()->create([
        'profile_id' => $profile->id,
        'source' => 'gallery-photo-to-delete',
        'position' => 0,
    ]);
    Storage::disk('public')->put('media/thumbnail/gallery-photo-to-delete.webp', 'fake');
    Storage::disk('public')->put('media/medium/gallery-photo-to-delete.webp', 'fake');

    Livewire::actingAs($owner)
        ->test('parts.profile.add-image-form', ['media_id' => $media->id])
        ->call('delete')
        ->assertDispatched('media-deleted')
        ->assertDispatched('close-modal');

    expect(Media::find($media->id))->toBeNull();
    Storage::disk('public')
        ->assertMissing('media/thumbnail/gallery-photo-to-delete.webp')
        ->assertMissing('media/medium/gallery-photo-to-delete.webp');
});

it('non-owner cannot delete a media', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);
    $media = Media::factory()->image()->create(['profile_id' => $profile->id, 'position' => 0]);
    $stranger = User::factory()->create();

    Livewire::actingAs($stranger)
        ->test('parts.profile.add-image-form', ['media_id' => $media->id])
        ->call('delete')
        ->assertForbidden();

    expect(Media::find($media->id))->not->toBeNull();
});
