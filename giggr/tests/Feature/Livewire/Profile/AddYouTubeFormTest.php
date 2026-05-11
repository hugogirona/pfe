<?php

use App\Enums\MediaType;
use App\Models\Media;
use App\Models\Profile;
use App\Models\User;
use Livewire\Livewire;

it('owner can add a youtube video by pasting a watch url', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('url', 'https://www.youtube.com/watch?v=cj9kbTU9pKA')
        ->set('caption', 'Mon dernier concert')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('media-added')
        ->assertDispatched('close-modal');

    expect(Media::count())->toBe(1)
        ->and(Media::first())
        ->type->toBe(MediaType::Youtube)
        ->source->toBe('cj9kbTU9pKA')
        ->caption->toBe('Mon dernier concert')
        ->profile_id->toBe($profile->id);
});

it('extracts the id from various url formats', function (string $url, string $expectedId) {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('url', $url)
        ->call('save')
        ->assertHasNoErrors();

    expect(Media::first()->source)->toBe($expectedId);
})->with([
    ['https://youtu.be/cj9kbTU9pKA', 'cj9kbTU9pKA'],
    ['https://www.youtube.com/embed/cj9kbTU9pKA', 'cj9kbTU9pKA'],
]);

it('assigns the next position automatically (max + 1)', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);
    Media::factory()->create(['profile_id' => $profile->id, 'position' => 0]);
    Media::factory()->create(['profile_id' => $profile->id, 'position' => 5]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('url', 'https://youtu.be/cj9kbTU9pKA')
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
        ->set('url', 'https://youtu.be/cj9kbTU9pKA')
        ->call('save');

    expect(Media::first()->position)->toBe(0);
});

it('rejects an invalid url', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('url', 'https://vimeo.com/12345')
        ->call('save')
        ->assertHasErrors(['url']);

    expect(Media::count())->toBe(0);
});

it('rejects an empty url', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('url', '')
        ->call('save')
        ->assertHasErrors(['url' => 'required']);
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
        ->set('url', 'https://www.youtube.com/watch?v=cj9kbTU9pKA')
        ->call('save')
        ->assertHasErrors(['url']);

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
        ->set('url', 'https://www.youtube.com/watch?v=cj9kbTU9pKA')
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
        ->set('url', 'https://youtu.be/cj9kbTU9pKA')
        ->call('save')
        ->assertHasErrors(['url']);

    expect(Media::count())->toBe(20);
});

it('non-owner cannot add a video', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);
    $stranger = User::factory()->create();

    Livewire::actingAs($stranger)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('url', 'https://youtu.be/cj9kbTU9pKA')
        ->call('save')
        ->assertForbidden();

    expect(Media::count())->toBe(0);
});

it('guest cannot add a video', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('url', 'https://youtu.be/cj9kbTU9pKA')
        ->call('save')
        ->assertForbidden();

    expect(Media::count())->toBe(0);
});

it('caption is optional', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('url', 'https://youtu.be/cj9kbTU9pKA')
        ->call('save')
        ->assertHasNoErrors();

    expect(Media::first()->caption)->toBeNull();
});

it('caption length is capped', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('url', 'https://youtu.be/cj9kbTU9pKA')
        ->set('caption', str_repeat('a', 256))
        ->call('save')
        ->assertHasErrors(['caption']);
});

it('resets the form after a successful save', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('parts.profile.add-youtube-form', ['model_id' => $profile->id])
        ->set('url', 'https://youtu.be/cj9kbTU9pKA')
        ->set('caption', 'something')
        ->call('save')
        ->assertSet('url', '')
        ->assertSet('caption', '');
});
