<?php

use App\Enums\MediaType;
use App\Models\Media;
use App\Models\Profile;
use Livewire\Livewire;

it('starts hidden with no media', function () {
    Livewire::test('parts.profile.media-lightbox')
        ->assertSet('open', false)
        ->assertSet('mediaId', null);
});

it('opens when receiving open-media-lightbox event for an image', function () {
    $profile = Profile::factory()->create();
    $media = Media::factory()->image()->create([
        'profile_id' => $profile->id,
        'source' => 'gallery-photo-abc',
        'caption' => 'Backstage shot',
        'width' => 1600,
        'height' => 900,
    ]);

    Livewire::test('parts.profile.media-lightbox')
        ->dispatch('open-media-lightbox', mediaId: $media->id)
        ->assertSet('open', true)
        ->assertSet('mediaId', $media->id)
        ->assertSee('Backstage shot');
});

it('opens when receiving open-media-lightbox event for a youtube video', function () {
    $profile = Profile::factory()->create();
    $media = Media::factory()->youtube()->create([
        'profile_id' => $profile->id,
        'source' => 'cj9kbTU9pKA',
        'caption' => 'Mon dernier live',
    ]);

    Livewire::test('parts.profile.media-lightbox')
        ->dispatch('open-media-lightbox', mediaId: $media->id)
        ->assertSet('open', true)
        ->assertSet('mediaId', $media->id)
        ->assertSee('Mon dernier live')
        ->assertSee('https://www.youtube.com/embed/cj9kbTU9pKA', escape: false);
});

it('exposes the loaded media via the media computed property', function () {
    $profile = Profile::factory()->create();
    $media = Media::factory()->image()->create(['profile_id' => $profile->id]);

    $component = Livewire::test('parts.profile.media-lightbox')
        ->dispatch('open-media-lightbox', mediaId: $media->id);

    expect($component->get('media'))->toBeInstanceOf(Media::class)
        ->and($component->get('media')->id)->toBe($media->id);
});

it('media is null when no lightbox is open', function () {
    $component = Livewire::test('parts.profile.media-lightbox');

    expect($component->get('media'))->toBeNull();
});

it('silently ignores an invalid media id', function () {
    Livewire::test('parts.profile.media-lightbox')
        ->dispatch('open-media-lightbox', mediaId: 99999)
        ->assertSet('open', false)
        ->assertSet('mediaId', null);
});

it('close() resets state', function () {
    $profile = Profile::factory()->create();
    $media = Media::factory()->image()->create(['profile_id' => $profile->id]);

    Livewire::test('parts.profile.media-lightbox')
        ->dispatch('open-media-lightbox', mediaId: $media->id)
        ->assertSet('open', true)
        ->call('close')
        ->assertSet('open', false);
});

it('renders the caption when present', function () {
    $profile = Profile::factory()->create();
    $media = Media::factory()->image()->create([
        'profile_id' => $profile->id,
        'caption' => 'Une légende unique',
    ]);

    Livewire::test('parts.profile.media-lightbox')
        ->dispatch('open-media-lightbox', mediaId: $media->id)
        ->assertSee('Une légende unique');
});

it('does not render a caption section when caption is null', function () {
    $profile = Profile::factory()->create();
    $media = Media::factory()->image()->create([
        'profile_id' => $profile->id,
        'caption' => null,
    ]);

    Livewire::test('parts.profile.media-lightbox')
        ->dispatch('open-media-lightbox', mediaId: $media->id)
        ->assertDontSeeText('caption-text-marker');
});

it('image media exposes width and height for aspect-ratio rendering', function () {
    $profile = Profile::factory()->create();
    $media = Media::factory()->image()->create([
        'profile_id' => $profile->id,
        'width' => 1920,
        'height' => 1080,
    ]);

    $component = Livewire::test('parts.profile.media-lightbox')
        ->dispatch('open-media-lightbox', mediaId: $media->id);

    expect($component->get('media')->width)->toBe(1920)
        ->and($component->get('media')->height)->toBe(1080);
});

it('determines the correct rendering type for image vs youtube', function () {
    $profile = Profile::factory()->create();
    $image = Media::factory()->image()->create(['profile_id' => $profile->id]);
    $youtube = Media::factory()->youtube()->create(['profile_id' => $profile->id, 'position' => 1]);

    $component = Livewire::test('parts.profile.media-lightbox');

    $component->dispatch('open-media-lightbox', mediaId: $image->id);
    expect($component->get('media')->type)->toBe(MediaType::Image);

    $component->dispatch('open-media-lightbox', mediaId: $youtube->id);
    expect($component->get('media')->type)->toBe(MediaType::Youtube);
});
