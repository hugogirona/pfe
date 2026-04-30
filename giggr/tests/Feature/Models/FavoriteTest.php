<?php

use App\Models\Announcement;
use App\Models\Favorite;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\QueryException;

it('belongs to a user', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create();
    $favorite = Favorite::create(['user_id' => $user->id, 'favoritable_type' => 'profile', 'favoritable_id' => $profile->id]);

    expect($favorite->user)->toBeInstanceOf(User::class)
        ->and($favorite->user->id)->toBe($user->id);
});

it('user has many favorites', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create();
    $announcement = Announcement::factory()->create();

    Favorite::create(['user_id' => $user->id, 'favoritable_type' => 'profile', 'favoritable_id' => $profile->id]);
    Favorite::create(['user_id' => $user->id, 'favoritable_type' => 'announcement', 'favoritable_id' => $announcement->id]);

    expect($user->favorites)->toHaveCount(2);
});

it('morphs to a Profile', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create();
    $favorite = Favorite::create(['user_id' => $user->id, 'favoritable_type' => 'profile', 'favoritable_id' => $profile->id]);

    expect($favorite->favoritable)->toBeInstanceOf(Profile::class)
        ->and($favorite->favoritable->id)->toBe($profile->id);
});

it('morphs to an Announcement', function () {
    $user = User::factory()->create();
    $announcement = Announcement::factory()->create();
    $favorite = Favorite::create(['user_id' => $user->id, 'favoritable_type' => 'announcement', 'favoritable_id' => $announcement->id]);

    expect($favorite->favoritable)->toBeInstanceOf(Announcement::class)
        ->and($favorite->favoritable->id)->toBe($announcement->id);
});

it('morph map stores profile string instead of class name', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create();
    $favorite = Favorite::create(['user_id' => $user->id, 'favoritable_type' => 'profile', 'favoritable_id' => $profile->id]);

    expect($favorite->fresh()->favoritable_type)->toBe('profile');
});

it('morph map stores announcement string instead of class name', function () {
    $user = User::factory()->create();
    $announcement = Announcement::factory()->create();
    $favorite = Favorite::create(['user_id' => $user->id, 'favoritable_type' => 'announcement', 'favoritable_id' => $announcement->id]);

    expect($favorite->fresh()->favoritable_type)->toBe('announcement');
});

it('enforces composite unique constraint on user + favoritable', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create();

    Favorite::create(['user_id' => $user->id, 'favoritable_type' => 'profile', 'favoritable_id' => $profile->id]);

    expect(fn () => Favorite::create(['user_id' => $user->id, 'favoritable_type' => 'profile', 'favoritable_id' => $profile->id]))
        ->toThrow(QueryException::class);
});

it('exposes a working factory', function () {
    $favorite = Favorite::factory()->create();

    expect($favorite)->toBeInstanceOf(Favorite::class)
        ->and($favorite->user_id)->not->toBeNull()
        ->and($favorite->favoritable)->not->toBeNull();
});
