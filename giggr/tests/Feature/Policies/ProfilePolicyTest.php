<?php

use App\Models\User;

it('lets the owner update their profile', function () {
    $owner = User::factory()->withProfile()->create();

    expect($owner->can('update', $owner->profile))->toBeTrue();
});

it('forbids a non-owner from updating a profile', function () {
    $owner = User::factory()->withProfile()->create();
    $other = User::factory()->create();

    expect($other->can('update', $owner->profile))->toBeFalse();
});
