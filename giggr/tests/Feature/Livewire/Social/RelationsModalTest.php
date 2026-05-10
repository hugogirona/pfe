<?php

use App\Models\Announcement;
use App\Models\Follow;
use App\Models\Profile;
use App\Models\User;
use Livewire\Livewire;

it('starts hidden', function () {
    $profile = Profile::factory()->create();

    Livewire::test('parts.social.relations-modal')
        ->assertSet('open', false)
        ->assertSet('profileId', null);
});

it('opens on the open-relations-modal event with default tab=followers', function () {
    $profile = Profile::factory()->create();

    Livewire::test('parts.social.relations-modal')
        ->dispatch('open-relations-modal', profileId: $profile->id)
        ->assertSet('open', true)
        ->assertSet('profileId', $profile->id)
        ->assertSet('activeTab', 'followers');
});

it('opens with the requested tab when provided', function () {
    $profile = Profile::factory()->create();

    Livewire::test('parts.social.relations-modal')
        ->dispatch('open-relations-modal', profileId: $profile->id, tab: 'followed')
        ->assertSet('open', true)
        ->assertSet('activeTab', 'followed');
});

it('rejects an invalid tab and falls back to followers', function () {
    $profile = Profile::factory()->create();

    Livewire::test('parts.social.relations-modal')
        ->dispatch('open-relations-modal', profileId: $profile->id, tab: 'nonsense')
        ->assertSet('activeTab', 'followers');
});

it('lists the profiles whose users follow this profile (followers tab)', function () {
    $owner = User::factory()->create(['first_name' => 'Owner']);
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    $follower1 = User::factory()->create(['first_name' => 'Alice']);
    Profile::factory()->create(['user_id' => $follower1->id]);
    $follower1->follow($profile);

    $follower2 = User::factory()->create(['first_name' => 'Bob']);
    Profile::factory()->create(['user_id' => $follower2->id]);
    $follower2->follow($profile);

    // Noise: a third user who does NOT follow
    $stranger = User::factory()->create(['first_name' => 'Stranger']);
    Profile::factory()->create(['user_id' => $stranger->id]);

    Livewire::test('parts.social.relations-modal')
        ->dispatch('open-relations-modal', profileId: $profile->id, tab: 'followers')
        ->assertSee('Alice')
        ->assertSee('Bob')
        ->assertDontSee('Stranger');
});

it('lists the profiles followed by this profile owner (following tab)', function () {
    $owner = User::factory()->create(['first_name' => 'Owner']);
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    $followed1User = User::factory()->create(['first_name' => 'Followed1']);
    $followed1 = Profile::factory()->create(['user_id' => $followed1User->id]);
    $followed2User = User::factory()->create(['first_name' => 'Followed2']);
    $followed2 = Profile::factory()->create(['user_id' => $followed2User->id]);
    $owner->follow($followed1);
    $owner->follow($followed2);

    // Noise: a profile the owner does NOT follow
    $strangerUser = User::factory()->create(['first_name' => 'Stranger']);
    Profile::factory()->create(['user_id' => $strangerUser->id]);

    Livewire::test('parts.social.relations-modal')
        ->dispatch('open-relations-modal', profileId: $profile->id, tab: 'followed')
        ->assertSee('Followed1')
        ->assertSee('Followed2')
        ->assertDontSee('Stranger');
});

it('following tab excludes followed announcements (only profiles)', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    $followedUser = User::factory()->create(['first_name' => 'FollowedMusician']);
    $followedProfile = Profile::factory()->create(['user_id' => $followedUser->id]);
    $owner->follow($followedProfile);

    $announcement = Announcement::factory()->create();
    $owner->follow($announcement);

    $component = Livewire::test('parts.social.relations-modal')
        ->dispatch('open-relations-modal', profileId: $profile->id, tab: 'followed');

    expect($component->get('followed')->count())->toBe(1);
});

it('switching tabs updates the displayed list', function () {
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    // Owner follows someone
    $followedUser = User::factory()->create(['first_name' => 'IFollow']);
    $followed = Profile::factory()->create(['user_id' => $followedUser->id]);
    $owner->follow($followed);

    // Someone follows the owner
    $followerUser = User::factory()->create(['first_name' => 'FollowsMe']);
    Profile::factory()->create(['user_id' => $followerUser->id]);
    $followerUser->follow($profile);

    Livewire::test('parts.social.relations-modal')
        ->dispatch('open-relations-modal', profileId: $profile->id, tab: 'followers')
        ->assertSee('FollowsMe')
        ->assertDontSee('IFollow')
        ->call('setTab', 'followed')
        ->assertSee('IFollow')
        ->assertDontSee('FollowsMe');
});

it('close() resets the open state', function () {
    $profile = Profile::factory()->create();

    Livewire::test('parts.social.relations-modal')
        ->dispatch('open-relations-modal', profileId: $profile->id)
        ->assertSet('open', true)
        ->call('close')
        ->assertSet('open', false);
});

it('renders empty state when no followers and tab is followers', function () {
    $profile = Profile::factory()->create();

    $component = Livewire::test('parts.social.relations-modal')
        ->dispatch('open-relations-modal', profileId: $profile->id, tab: 'followers');

    expect($component->get('followers')->count())->toBe(0);
});

it('shows the visitor empty message when viewing someone elses profile (followers)', function () {
    $viewer = User::factory()->create();
    $other = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $other->id]);

    Livewire::actingAs($viewer)
        ->test('parts.social.relations-modal')
        ->dispatch('open-relations-modal', profileId: $profile->id, tab: 'followers')
        ->assertSet('isOwnProfile', false)
        ->assertSee(__('social.no_followers'))
        ->assertDontSee(__('social.no_followers_own'));
});

it('shows the visitor empty message when viewing someone elses profile (following)', function () {
    $viewer = User::factory()->create();
    $other = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $other->id]);

    Livewire::actingAs($viewer)
        ->test('parts.social.relations-modal')
        ->dispatch('open-relations-modal', profileId: $profile->id, tab: 'followed')
        ->assertSee(__('social.no_followed'))
        ->assertDontSee(__('social.no_followed_own'));
});

it('shows the own empty message when viewing my own profile (followers)', function () {
    $viewer = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $viewer->id]);

    Livewire::actingAs($viewer)
        ->test('parts.social.relations-modal')
        ->dispatch('open-relations-modal', profileId: $profile->id, tab: 'followers')
        ->assertSet('isOwnProfile', true)
        ->assertSee(__('social.no_followers_own'));
});

it('shows the own empty message when viewing my own profile (following)', function () {
    $viewer = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $viewer->id]);

    Livewire::actingAs($viewer)
        ->test('parts.social.relations-modal')
        ->dispatch('open-relations-modal', profileId: $profile->id, tab: 'followed')
        ->assertSee(__('social.no_followed_own'));
});

it('preloads the viewer follow state for each row in the list', function () {
    $viewer = User::factory()->create();
    $owner = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $owner->id]);

    // Build 2 followers of the profile, and have the viewer already follow one of them
    $followerUser1 = User::factory()->create();
    $follower1 = Profile::factory()->create(['user_id' => $followerUser1->id]);
    $followerUser1->follow($profile);

    $followerUser2 = User::factory()->create();
    $follower2 = Profile::factory()->create(['user_id' => $followerUser2->id]);
    $followerUser2->follow($profile);

    $viewer->follow($follower1);

    $component = Livewire::actingAs($viewer)
        ->test('parts.social.relations-modal')
        ->dispatch('open-relations-modal', profileId: $profile->id, tab: 'followers');

    expect($component->get('followedIds'))->toContain($follower1->id)
        ->and($component->get('followedIds'))->not->toContain($follower2->id);
});

it('toggleFollow creates a follow row and updates followedIds', function () {
    $viewer = User::factory()->create();
    $profile = Profile::factory()->create();
    $target = Profile::factory()->create();
    $target->user->follow($profile);

    $component = Livewire::actingAs($viewer)
        ->test('parts.social.relations-modal')
        ->dispatch('open-relations-modal', profileId: $profile->id, tab: 'followers')
        ->call('toggleFollow', $target->id);

    expect(Follow::where('user_id', $viewer->id)
        ->where('followable_type', 'profile')
        ->where('followable_id', $target->id)
        ->exists())->toBeTrue()
        ->and($component->get('followedIds'))->toContain($target->id);
});

it('toggleFollow removes the follow row when called twice', function () {
    $viewer = User::factory()->create();
    $profile = Profile::factory()->create();
    $target = Profile::factory()->create();
    $target->user->follow($profile);

    $component = Livewire::actingAs($viewer)
        ->test('parts.social.relations-modal')
        ->dispatch('open-relations-modal', profileId: $profile->id, tab: 'followers')
        ->call('toggleFollow', $target->id)
        ->call('toggleFollow', $target->id);

    expect(Follow::where('user_id', $viewer->id)
        ->where('followable_type', 'profile')
        ->where('followable_id', $target->id)
        ->exists())->toBeFalse()
        ->and($component->get('followedIds'))->not->toContain($target->id);
});

it('toggleFollow is a no-op on the viewers own profile', function () {
    $viewer = User::factory()->create();
    $viewerProfile = Profile::factory()->create(['user_id' => $viewer->id]);
    $profile = Profile::factory()->create();
    $viewer->follow($profile);

    Livewire::actingAs($viewer)
        ->test('parts.social.relations-modal')
        ->dispatch('open-relations-modal', profileId: $profile->id, tab: 'followers')
        ->call('toggleFollow', $viewerProfile->id);

    expect(Follow::where('user_id', $viewer->id)
        ->where('followable_id', $viewerProfile->id)
        ->exists())->toBeFalse();
});

it('guest cannot call toggleFollow', function () {
    $profile = Profile::factory()->create();
    $target = Profile::factory()->create();

    Livewire::test('parts.social.relations-modal')
        ->dispatch('open-relations-modal', profileId: $profile->id, tab: 'followers')
        ->call('toggleFollow', $target->id)
        ->assertForbidden();

    expect(Follow::count())->toBe(0);
});

it('dispatches follow-state-changed after a successful toggleFollow', function () {
    $viewer = User::factory()->create();
    $profile = Profile::factory()->create();
    $target = Profile::factory()->create();
    $target->user->follow($profile);

    Livewire::actingAs($viewer)
        ->test('parts.social.relations-modal')
        ->dispatch('open-relations-modal', profileId: $profile->id, tab: 'followers')
        ->call('toggleFollow', $target->id)
        ->assertDispatched('follow-state-changed');
});
