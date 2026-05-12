<?php

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Contracts\Broadcasting\Broadcaster;

function conversationChannelCallback(): Closure
{
    $broadcaster = app(Broadcaster::class);
    $reflection = new ReflectionClass($broadcaster);
    $property = $reflection->getProperty('channels');
    $channels = $property->getValue($broadcaster);

    return $channels['conversation.{conversationId}'];
}

it('authorizes a participant on the conversation channel', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);

    expect((conversationChannelCallback())($alice, $convo->id))->toBeTrue()
        ->and((conversationChannelCallback())($bob, $convo->id))->toBeTrue();
});

it('rejects an outsider on the conversation channel', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $eve = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);

    expect((conversationChannelCallback())($eve, $convo->id))->toBeFalse();
});

it('rejects access when the conversation does not exist', function () {
    $alice = User::factory()->withProfile()->create();

    expect((conversationChannelCallback())($alice, 999999))->toBeFalse();
});
