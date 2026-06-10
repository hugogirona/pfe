<?php

use App\Models\Follow;
use App\Models\Profile;
use App\Models\User;
use App\Notifications\NewFollower;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

it('exposes the follow affordance via its accessible label when not following', function () {
    $viewer = User::factory()->create();
    $profile = Profile::factory()->create();

    Livewire::actingAs($viewer)
        ->test('parts.social.follow-button', ['profileId' => $profile->id])
        ->assertSet('isFollowing', false)
        ->assertSee('aria-pressed="false"', false)
        ->assertSee(__('social.follow_aria', ['name' => $profile->user->full_name]));
});

it('reflects the following state via aria-pressed and the unfollow label', function () {
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
        ->assertSee('aria-pressed="true"', false)
        ->assertSee(__('social.unfollow_aria', ['name' => $profile->user->full_name]));
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

it('uses preloaded props instead of querying when all are provided', function () {
    $viewer = User::factory()->create();
    $profile = Profile::factory()->create();
    $component = Livewire::actingAs($viewer)
        ->test('parts.social.follow-button', [
            'profileId' => $profile->id,
            'musicianName' => 'Cached Display Name',
            'ownerId' => $profile->user_id,
            'isFollowing' => true,
        ]);

    expect($component->get('musicianName'))->toBe('Cached Display Name')
        ->and($component->get('ownerId'))->toBe($profile->user_id)
        ->and($component->get('isFollowing'))->toBeTrue();
});

it('preloaded ownerId still hides the button when viewer owns the profile', function () {
    $self = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $self->id]);

    Livewire::actingAs($self)
        ->test('parts.social.follow-button', [
            'profileId' => $profile->id,
            'musicianName' => 'whatever',
            'ownerId' => $self->id,
            'isFollowing' => false,
        ])
        ->assertSet('isOwn', true);
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

it('dispatches follow-state-changed after a successful toggle', function () {
    $viewer = User::factory()->create();
    $profile = Profile::factory()->create();

    Livewire::actingAs($viewer)
        ->test('parts.social.follow-button', ['profileId' => $profile->id])
        ->call('toggle')
        ->assertDispatched('follow-state-changed');
});

it('notifies the followed user on a new follow', function () {
    Notification::fake();
    $viewer = User::factory()->withProfile()->create();
    $profile = Profile::factory()->create();

    Livewire::actingAs($viewer)
        ->test('parts.social.follow-button', ['profileId' => $profile->id])
        ->call('toggle');

    Notification::assertSentTo($profile->user, NewFollower::class);
});

it('does not notify when unfollowing', function () {
    $viewer = User::factory()->withProfile()->create();
    $profile = Profile::factory()->create();
    $viewer->follow($profile);

    Notification::fake();

    Livewire::actingAs($viewer)
        ->test('parts.social.follow-button', ['profileId' => $profile->id])
        ->call('toggle');

    Notification::assertNothingSent();
});

it('stores a database notification linking to the follower profile', function () {
    $viewer = User::factory()->withProfile()->create();
    $profile = Profile::factory()->create();

    Livewire::actingAs($viewer)
        ->test('parts.social.follow-button', ['profileId' => $profile->id])
        ->call('toggle');

    $notification = $profile->user->notifications()->first();

    expect($notification)->not->toBeNull()
        ->and($notification->data['actor_profile_id'])->toBe($viewer->profile->id)
        ->and($notification->data['actor_name'])->toBe($viewer->full_name);
});
