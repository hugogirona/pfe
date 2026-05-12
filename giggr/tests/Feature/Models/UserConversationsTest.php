<?php

use App\Models\Conversation;
use App\Models\User;

it('user can list conversations from either side of the pair', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    $convo = Conversation::between($alice, $bob);

    expect($alice->conversations->pluck('id')->all())->toBe([$convo->id])
        ->and($bob->conversations->pluck('id')->all())->toBe([$convo->id]);
});

it('outsider users do not see the conversation', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    $eve = User::factory()->create();
    Conversation::between($alice, $bob);

    expect($eve->conversations->all())->toBe([]);
});

it('conversation pivot exposes last_read_at and hidden_at state per user', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    $convo = Conversation::between($alice, $bob);

    $alice->conversations()->updateExistingPivot($convo->id, [
        'last_read_at' => now(),
        'hidden_at' => now(),
    ]);

    $pivot = $alice->conversations()->find($convo->id)->pivot;
    expect($pivot->last_read_at)->not->toBeNull()
        ->and($pivot->hidden_at)->not->toBeNull();
});
