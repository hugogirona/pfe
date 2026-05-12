<?php

use App\Broadcasting\ConversationChannel;
use App\Models\Conversation;
use App\Models\User;

it('authorizes a participant on the conversation channel', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);

    $channel = new ConversationChannel;

    expect($channel->join($alice, $convo->id))->toBeTrue()
        ->and($channel->join($bob, $convo->id))->toBeTrue();
});

it('rejects an outsider on the conversation channel', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $eve = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);

    expect((new ConversationChannel)->join($eve, $convo->id))->toBeFalse();
});

it('rejects access when the conversation does not exist', function () {
    $alice = User::factory()->withProfile()->create();

    expect((new ConversationChannel)->join($alice, 999999))->toBeFalse();
});
