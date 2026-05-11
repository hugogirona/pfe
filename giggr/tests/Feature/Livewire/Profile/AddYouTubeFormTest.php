<?php

use App\Enums\MediaType;
use App\Models\Media;
use App\Models\Profile;
use App\Models\User;
use Livewire\Livewire;

it('owner can add a youtube video by pasting the 11-char id', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('videoId', 'cj9kbTU9pKA')
        ->set('caption', 'Mon dernier concert')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('media-added')
        ->assertNotDispatched('close-modal')
        ->assertSet('success', true);

    expect(Media::count())->toBe(1)
        ->and(Media::first())
        ->type->toBe(MediaType::Youtube)
        ->source->toBe('cj9kbTU9pKA')
        ->caption->toBe('Mon dernier concert')
        ->profile_id->toBe($profile->id);
});

it('close action dispatches close-modal', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('videoId', 'cj9kbTU9pKA')
        ->call('save')
        ->call('close')
        ->assertDispatched('close-modal');
});

it('shows the success message after save', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('videoId', 'cj9kbTU9pKA')
        ->call('save')
        ->assertSee(__('profile.add_youtube_success_title'))
        ->assertSee(__('profile.add_youtube_success_body'))
        ->assertSee(__('profile.add_youtube_close'));
});

it('accepts ids with hyphens and underscores', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('videoId', 'AB_cd-EF1gh')
        ->call('save')
        ->assertHasNoErrors();

    expect(Media::first()->source)->toBe('AB_cd-EF1gh');
});

it('assigns the next position automatically (max + 1)', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);
    Media::factory()->create(['profile_id' => $profile->id, 'position' => 0]);
    Media::factory()->create(['profile_id' => $profile->id, 'position' => 5]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('videoId', 'cj9kbTU9pKA')
        ->call('save')
        ->assertHasNoErrors();

    expect(Media::orderByDesc('position')->first())
        ->source->toBe('cj9kbTU9pKA')
        ->position->toBe(6);
});

it('assigns position 0 to the first media on the profile', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('videoId', 'cj9kbTU9pKA')
        ->call('save');

    expect(Media::first()->position)->toBe(0);
});

it('rejects a url instead of an id', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('videoId', 'https://www.youtube.com/watch?v=cj9kbTU9pKA')
        ->call('save')
        ->assertHasErrors(['videoId']);

    expect(Media::count())->toBe(0);
});

it('rejects an id shorter than 11 chars', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('videoId', 'tooshort')
        ->call('save')
        ->assertHasErrors(['videoId']);
});

it('rejects an id longer than 11 chars', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('videoId', 'thisidistoolong')
        ->call('save')
        ->assertHasErrors(['videoId']);
});

it('rejects an id with invalid characters', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('videoId', 'cj9kbT!9pKA')
        ->call('save')
        ->assertHasErrors(['videoId']);
});

it('rejects an empty id', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('videoId', '')
        ->call('save')
        ->assertHasErrors(['videoId' => 'required']);
});

it('rejects a duplicate youtube id on the same profile', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);
    Media::factory()->create([
        'profile_id' => $profile->id,
        'type' => MediaType::Youtube,
        'source' => 'cj9kbTU9pKA',
        'position' => 0,
    ]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('videoId', 'cj9kbTU9pKA')
        ->call('save')
        ->assertHasErrors(['videoId']);

    expect(Media::count())->toBe(1);
});

it('allows the same youtube id on different profiles', function () {
    $owner1 = User::factory()->create();
    $profile1 = Profile::factory()->create(['user_id' => $owner1->id]);
    Media::factory()->create([
        'profile_id' => $profile1->id,
        'type' => MediaType::Youtube,
        'source' => 'cj9kbTU9pKA',
        'position' => 0,
    ]);

    $owner2 = User::factory()->create();
    $profile2 = Profile::factory()->create(['user_id' => $owner2->id]);

    Livewire::actingAs($owner2)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile2->id])
        ->set('videoId', 'cj9kbTU9pKA')
        ->call('save')
        ->assertHasNoErrors();

    expect(Media::count())->toBe(2);
});

it('rejects when the profile already has 20 medias', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);
    Media::factory()
        ->count(20)
        ->sequence(fn ($s) => ['position' => $s->index])
        ->create(['profile_id' => $profile->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('videoId', 'cj9kbTU9pKA')
        ->call('save')
        ->assertHasErrors(['videoId']);

    expect(Media::count())->toBe(20);
});

it('non-owner cannot add a video', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);
    $stranger = User::factory()->create();

    Livewire::actingAs($stranger)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('videoId', 'cj9kbTU9pKA')
        ->call('save')
        ->assertForbidden();

    expect(Media::count())->toBe(0);
});

it('guest cannot add a video', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('videoId', 'cj9kbTU9pKA')
        ->call('save')
        ->assertForbidden();

    expect(Media::count())->toBe(0);
});

it('caption is optional', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('videoId', 'cj9kbTU9pKA')
        ->call('save')
        ->assertHasNoErrors();

    expect(Media::first()->caption)->toBeNull();
});

it('caption length is capped', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('videoId', 'cj9kbTU9pKA')
        ->set('caption', str_repeat('a', 256))
        ->call('save')
        ->assertHasErrors(['caption']);
});
