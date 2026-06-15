<?php

use App\Models\Conversation;
use App\Models\User;

it('lets a participant view and delete the conversation', function () {
    $requester = User::factory()->create();
    $recipient = User::factory()->create();
    $conversation = Conversation::factory()->create([
        'user_a_id' => $requester,
        'user_b_id' => $recipient,
        'requester_user_id' => $requester,
    ]);

    expect($requester->can('view', $conversation))->toBeTrue()
        ->and($recipient->can('view', $conversation))->toBeTrue()
        ->and($requester->can('delete', $conversation))->toBeTrue();
});

it('forbids an outsider from viewing or deleting the conversation', function () {
    $conversation = Conversation::factory()->create();
    $outsider = User::factory()->create();

    expect($outsider->can('view', $conversation))->toBeFalse()
        ->and($outsider->can('delete', $conversation))->toBeFalse();
});

it('lets the recipient respond to a pending request but not the requester', function () {
    $requester = User::factory()->create();
    $recipient = User::factory()->create();
    $conversation = Conversation::factory()->create([
        'user_a_id' => $requester,
        'user_b_id' => $recipient,
        'requester_user_id' => $requester,
    ]);

    expect($recipient->can('respondToRequest', $conversation))->toBeTrue()
        ->and($requester->can('respondToRequest', $conversation))->toBeFalse();
});

it('forbids an outsider from responding to a request', function () {
    $conversation = Conversation::factory()->create();
    $outsider = User::factory()->create();

    expect($outsider->can('respondToRequest', $conversation))->toBeFalse();
});
