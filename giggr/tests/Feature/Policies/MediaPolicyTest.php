<?php

use App\Models\Media;
use App\Models\User;

it('lets the profile owner update and delete their media', function () {
    $owner = User::factory()->withProfile()->create();
    $media = Media::factory()->for($owner->profile)->create();

    expect($owner->can('update', $media))->toBeTrue()
        ->and($owner->can('delete', $media))->toBeTrue();
});

it('forbids a non-owner from updating or deleting media', function () {
    $owner = User::factory()->withProfile()->create();
    $other = User::factory()->create();
    $media = Media::factory()->for($owner->profile)->create();

    expect($other->can('update', $media))->toBeFalse()
        ->and($other->can('delete', $media))->toBeFalse();
});
