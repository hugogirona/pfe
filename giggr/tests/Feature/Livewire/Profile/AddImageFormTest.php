<?php

use App\Enums\MediaType;
use App\Models\Media;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
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

it('generates thumbnail and medium variants on disk', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-image-form', ['model_id' => $profile->id])
        ->set('photo', UploadedFile::fake()->image('photo.jpg', 800, 600))
        ->call('save');

    $media = Media::first();
    Storage::disk('public')
        ->assertExists("media/thumbnail/{$media->source}.webp")
        ->assertExists("media/medium/{$media->source}.webp");
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
