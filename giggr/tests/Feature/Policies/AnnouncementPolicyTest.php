<?php

use App\Models\Announcement;
use App\Models\User;

it('lets any authenticated user create an announcement', function () {
    $user = User::factory()->create();

    expect($user->can('create', Announcement::class))->toBeTrue();
});

it('lets the owner update and delete their announcement', function () {
    $owner = User::factory()->create();
    $announcement = Announcement::factory()->for($owner)->create();

    expect($owner->can('update', $announcement))->toBeTrue()
        ->and($owner->can('delete', $announcement))->toBeTrue();
});

it('forbids a non-owner from updating or deleting an announcement', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $announcement = Announcement::factory()->for($owner)->create();

    expect($other->can('update', $announcement))->toBeFalse()
        ->and($other->can('delete', $announcement))->toBeFalse();
});
