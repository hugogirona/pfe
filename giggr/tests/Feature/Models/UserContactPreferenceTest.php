<?php

use App\Enums\ContactPreference;
use App\Models\User;

it('lets any user make contact under the everyone preference', function () {
    $owner = User::factory()->withProfile()->create();
    $viewer = User::factory()->withProfile()->create();

    expect($owner->canBeContactedBy($viewer))->toBeTrue();
});

it('blocks all contact under the nobody preference', function () {
    $owner = User::factory()->withProfile()->create();
    $owner->profile->update(['contact_preference' => ContactPreference::Nobody]);
    $viewer = User::factory()->withProfile()->create();

    expect($owner->canBeContactedBy($viewer))->toBeFalse();
});

it('allows contact only from users the owner follows under followers_only', function () {
    $owner = User::factory()->withProfile()->create();
    $owner->profile->update(['contact_preference' => ContactPreference::FollowersOnly]);
    $followed = User::factory()->withProfile()->create();
    $stranger = User::factory()->withProfile()->create();

    $owner->follow($followed->profile);

    expect($owner->canBeContactedBy($followed))->toBeTrue()
        ->and($owner->canBeContactedBy($stranger))->toBeFalse();
});
