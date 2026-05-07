<?php

use App\Models\Follow;
use App\Models\Profile;
use App\Models\User;
use Livewire\Livewire;

it('shows Suivre when the viewer is not following the profile', function () {
    $viewer = User::factory()->create();
    $profile = Profile::factory()->create();

    Livewire::actingAs($viewer)
        ->test('parts.social.follow-button', ['profileId' => $profile->id])
        ->assertSet('isFollowing', false)
        ->assertSee(__('social.follow'));
});

it('shows Suivi when the viewer is already following the profile', function () {
    $viewer = User::factory()->create();
    $profile = Profile::factory()->create();
    Follow::create([
        'user_id' => $viewer->id,
        'followable_type' => 'profile',
        'followable_id' => $profile->id,
    ]);

    Livewire::actingAs($viewer)
        ->test('parts.social.follow-button', ['profileId' => $profile->id])
        ->assertSet('isFollowing', true)
        ->assertSee(__('social.following'));
});

it('toggling creates a follow row and flips the state', function () {
    $viewer = User::factory()->create();
    $profile = Profile::factory()->create();

    Livewire::actingAs($viewer)
        ->test('parts.social.follow-button', ['profileId' => $profile->id])
        ->call('toggle')
        ->assertSet('isFollowing', true);

    expect(Follow::where('user_id', $viewer->id)
        ->where('followable_type', 'profile')
        ->where('followable_id', $profile->id)
        ->exists())->toBeTrue();
});

it('toggling twice removes the follow row', function () {
    $viewer = User::factory()->create();
    $profile = Profile::factory()->create();

    Livewire::actingAs($viewer)
        ->test('parts.social.follow-button', ['profileId' => $profile->id])
        ->call('toggle')
        ->call('toggle')
        ->assertSet('isFollowing', false);

    expect(Follow::count())->toBe(0);
});

it('rapid toggling keeps the database consistent', function () {
    $viewer = User::factory()->create();
    $profile = Profile::factory()->create();

    $component = Livewire::actingAs($viewer)
        ->test('parts.social.follow-button', ['profileId' => $profile->id]);

    foreach (range(1, 5) as $i) {
        $component->call('toggle');
    }

    expect(Follow::where('user_id', $viewer->id)
        ->where('followable_type', 'profile')
        ->where('followable_id', $profile->id)
        ->count())
        ->toBeLessThanOrEqual(1);
});

it('guest cannot toggle', function () {
    $profile = Profile::factory()->create();

    Livewire::test('parts.social.follow-button', ['profileId' => $profile->id])
        ->call('toggle')
        ->assertForbidden();

    expect(Follow::count())->toBe(0);
});

it('hides itself when the viewer owns the profile', function () {
    $self = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $self->id]);

    Livewire::actingAs($self)
        ->test('parts.social.follow-button', ['profileId' => $profile->id])
        ->assertSet('isOwn', true)
        ->assertDontSee(__('social.follow'))
        ->assertDontSee(__('social.following'));
});

it('renders the button variant with full text label when variant=button', function () {
    $viewer = User::factory()->create();
    $profile = Profile::factory()->create();

    Livewire::actingAs($viewer)
        ->test('parts.social.follow-button', ['profileId' => $profile->id, 'variant' => 'button'])
        ->assertSet('variant', 'button')
        ->assertSee(__('social.follow'));
});

it('button variant toggles state and persists the follow', function () {
    $viewer = User::factory()->create();
    $profile = Profile::factory()->create();

    Livewire::actingAs($viewer)
        ->test('parts.social.follow-button', ['profileId' => $profile->id, 'variant' => 'button'])
        ->call('toggle')
        ->assertSet('isFollowing', true)
        ->assertSee(__('social.following'));

    expect(Follow::where('user_id', $viewer->id)
        ->where('followable_type', 'profile')
        ->where('followable_id', $profile->id)
        ->exists())->toBeTrue();
});
