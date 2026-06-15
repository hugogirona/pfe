<?php

use App\Enums\ContactPreference;
use App\Models\User;

it('allows contact when the recipient accepts everyone', function () {
    $recipient = User::factory()->withProfile()->create();
    $viewer = User::factory()->withProfile()->create();

    expect($viewer->can('contact', $recipient))->toBeTrue();
});

it('forbids contact when the recipient accepts nobody', function () {
    $recipient = User::factory()->withProfile()->create();
    $recipient->profile->update(['contact_preference' => ContactPreference::Nobody]);
    $viewer = User::factory()->withProfile()->create();

    expect($viewer->can('contact', $recipient))->toBeFalse();
});

it('forbids contacting oneself', function () {
    $user = User::factory()->withProfile()->create();

    expect($user->can('contact', $user))->toBeFalse();
});

it('lets a user block anyone but themselves', function () {
    $user = User::factory()->create();
    $target = User::factory()->create();

    expect($user->can('block', $target))->toBeTrue()
        ->and($user->can('block', $user))->toBeFalse();
});
