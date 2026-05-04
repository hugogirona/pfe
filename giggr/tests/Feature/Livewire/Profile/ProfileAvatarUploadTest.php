<?php

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

it('owner can upload a valid image', function () {
    Storage::fake('local');
    Storage::fake('public');

    $profile = Profile::factory()->create(['avatar_path' => null]);

    Livewire::actingAs($profile->user)
        ->test('parts.profile.avatar-form', ['model_id' => (string) $profile->id])
        ->set('photo', UploadedFile::fake()->image('avatar.jpg', 400, 400))
        ->call('save')
        ->assertDispatched('avatar-saved');

    expect($profile->fresh()->avatar_path)->not->toBeNull();
});

it('photo is required', function () {
    $profile = Profile::factory()->create();

    Livewire::actingAs($profile->user)
        ->test('parts.profile.avatar-form', ['model_id' => (string) $profile->id])
        ->call('save')
        ->assertHasErrors(['photo']);
});

it('photo must be an image', function () {
    $profile = Profile::factory()->create();

    Livewire::actingAs($profile->user)
        ->test('parts.profile.avatar-form', ['model_id' => (string) $profile->id])
        ->set('photo', UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'))
        ->call('save')
        ->assertHasErrors(['photo']);
});

it('photo cannot exceed 5 MB', function () {
    $profile = Profile::factory()->create();

    Livewire::actingAs($profile->user)
        ->test('parts.profile.avatar-form', ['model_id' => (string) $profile->id])
        ->set('photo', UploadedFile::fake()->image('avatar.jpg')->size(6000))
        ->call('save')
        ->assertHasErrors(['photo']);
});

it('non-owner cannot upload an avatar', function () {
    Storage::fake('local');
    Storage::fake('public');

    $profile = Profile::factory()->create(['avatar_path' => null]);
    $visitor = User::factory()->create();

    Livewire::actingAs($visitor)
        ->test('parts.profile.avatar-form', ['model_id' => (string) $profile->id])
        ->set('photo', UploadedFile::fake()->image('avatar.jpg', 400, 400))
        ->call('save')
        ->assertForbidden();

    expect($profile->fresh()->avatar_path)->toBeNull();
});
