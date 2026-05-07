<?php

use App\Models\Announcement;
use App\Models\Follow;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\QueryException;

it('belongs to a user', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create();
    $follow = Follow::create(['user_id' => $user->id, 'followable_type' => 'profile', 'followable_id' => $profile->id]);

    expect($follow->user)->toBeInstanceOf(User::class)
        ->and($follow->user->id)->toBe($user->id);
});

it('user has many follows', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create();
    $announcement = Announcement::factory()->create();

    Follow::create(['user_id' => $user->id, 'followable_type' => 'profile', 'followable_id' => $profile->id]);
    Follow::create(['user_id' => $user->id, 'followable_type' => 'announcement', 'followable_id' => $announcement->id]);

    expect($user->follows)->toHaveCount(2);
});

it('morphs to a Profile', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create();
    $follow = Follow::create(['user_id' => $user->id, 'followable_type' => 'profile', 'followable_id' => $profile->id]);

    expect($follow->followable)->toBeInstanceOf(Profile::class)
        ->and($follow->followable->id)->toBe($profile->id);
});

it('morphs to an Announcement', function () {
    $user = User::factory()->create();
    $announcement = Announcement::factory()->create();
    $follow = Follow::create(['user_id' => $user->id, 'followable_type' => 'announcement', 'followable_id' => $announcement->id]);

    expect($follow->followable)->toBeInstanceOf(Announcement::class)
        ->and($follow->followable->id)->toBe($announcement->id);
});

it('morph map stores profile string instead of class name', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create();
    $follow = Follow::create(['user_id' => $user->id, 'followable_type' => 'profile', 'followable_id' => $profile->id]);

    expect($follow->fresh()->followable_type)->toBe('profile');
});

it('morph map stores announcement string instead of class name', function () {
    $user = User::factory()->create();
    $announcement = Announcement::factory()->create();
    $follow = Follow::create(['user_id' => $user->id, 'followable_type' => 'announcement', 'followable_id' => $announcement->id]);

    expect($follow->fresh()->followable_type)->toBe('announcement');
});

it('enforces composite unique constraint on user + followable', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create();

    Follow::create(['user_id' => $user->id, 'followable_type' => 'profile', 'followable_id' => $profile->id]);

    expect(fn () => Follow::create(['user_id' => $user->id, 'followable_type' => 'profile', 'followable_id' => $profile->id]))
        ->toThrow(QueryException::class);
});

it('exposes a working factory', function () {
    $follow = Follow::factory()->create();

    expect($follow)->toBeInstanceOf(Follow::class)
        ->and($follow->user_id)->not->toBeNull()
        ->and($follow->followable)->not->toBeNull();
});

it('User::follow() creates a polymorphic row for a profile', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create();

    $user->follow($profile);

    expect(Follow::where('user_id', $user->id)
        ->where('followable_type', 'profile')
        ->where('followable_id', $profile->id)
        ->exists())->toBeTrue();
});

it('User::follow() is idempotent', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create();

    $user->follow($profile);
    $user->follow($profile);

    expect(Follow::where('user_id', $user->id)
        ->where('followable_type', 'profile')
        ->where('followable_id', $profile->id)
        ->count())->toBe(1);
});

it('User::unfollow() deletes the row', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create();

    $user->follow($profile);
    $user->unfollow($profile);

    expect(Follow::count())->toBe(0);
});

it('User::isFollowing() reflects the current relation', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create();

    expect($user->isFollowing($profile))->toBeFalse();

    $user->follow($profile);

    expect($user->isFollowing($profile))->toBeTrue();

    $user->unfollow($profile);

    expect($user->isFollowing($profile))->toBeFalse();
});

it('User::follow() works for Announcement too', function () {
    $user = User::factory()->create();
    $announcement = Announcement::factory()->create();

    $user->follow($announcement);

    expect($user->isFollowing($announcement))->toBeTrue()
        ->and(Follow::where('followable_type', 'announcement')->count())->toBe(1);
});
