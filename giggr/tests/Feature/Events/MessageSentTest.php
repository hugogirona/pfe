<?php

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

it('implements ShouldBroadcastNow for synchronous broadcasting', function () {
    expect(new MessageSent(Message::factory()->make()))
        ->toBeInstanceOf(ShouldBroadcastNow::class);
});

it('broadcasts on a private conversation channel and the recipient user channel', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $message = Message::factory()->for($convo)->for($alice, 'sender')->create();

    $channels = (new MessageSent($message))->broadcastOn();
    $channelNames = collect($channels)->map(fn (PrivateChannel $c) => $c->name)->all();

    expect($channels)->toHaveCount(2)
        ->and($channels[0])->toBeInstanceOf(PrivateChannel::class)
        ->and($channelNames)->toContain('private-conversation.'.$convo->id)
        ->and($channelNames)->toContain('private-App.Models.User.'.$bob->id);
});

it('broadcasts a serialised message payload', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $message = Message::factory()
        ->for($convo)
        ->for($alice, 'sender')
        ->create(['body' => 'Salut !']);

    $payload = (new MessageSent($message))->broadcastWith();

    expect($payload)
        ->toHaveKeys(['id', 'conversation_id', 'sender_id', 'body', 'created_at', 'read_at'])
        ->and($payload['id'])->toBe($message->id)
        ->and($payload['conversation_id'])->toBe($convo->id)
        ->and($payload['sender_id'])->toBe($alice->id)
        ->and($payload['body'])->toBe('Salut !')
        ->and($payload['read_at'])->toBeNull();
});

it('uses a short broadcast event name', function () {
    expect((new MessageSent(Message::factory()->make()))->broadcastAs())
        ->toBe('message.sent');
});
