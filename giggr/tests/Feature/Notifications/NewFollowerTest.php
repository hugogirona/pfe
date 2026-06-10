<?php

use App\Models\User;
use App\Notifications\NewFollower;

it('is delivered through the database channel', function () {
    $follower = User::factory()->withProfile()->create();

    expect((new NewFollower($follower))->via(User::factory()->make()))->toBe(['database']);
});

it('stores the actor identity and profile link in its payload', function () {
    $follower = User::factory()->withProfile()->create();

    $data = (new NewFollower($follower))->toArray(User::factory()->make());

    expect($data)
        ->toHaveKeys(['actor_user_id', 'actor_profile_id', 'actor_name'])
        ->and($data['actor_user_id'])->toBe($follower->id)
        ->and($data['actor_profile_id'])->toBe($follower->profile->id)
        ->and($data['actor_name'])->toBe($follower->full_name);
});
