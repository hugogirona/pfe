<?php

use App\Models\User;
use App\Notifications\NewFollower;

it('is delivered through the database channel', function () {
    $follower = User::factory()->withProfile()->create();

    expect((new NewFollower($follower))->via(User::factory()->make()))->toBe(['database']);
});

it('stores the follower identity and profile link in its payload', function () {
    $follower = User::factory()->withProfile()->create();

    $data = (new NewFollower($follower))->toArray(User::factory()->make());

    expect($data)
        ->toHaveKeys(['follower_user_id', 'follower_profile_id', 'follower_name'])
        ->and($data['follower_user_id'])->toBe($follower->id)
        ->and($data['follower_profile_id'])->toBe($follower->profile->id)
        ->and($data['follower_name'])->toBe($follower->full_name);
});
