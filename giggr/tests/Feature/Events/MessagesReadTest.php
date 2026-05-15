<?php

use App\Events\MessagesRead;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

it('implements ShouldBroadcastNow for synchronous broadcasting', function () {
    expect(new MessagesRead(1, 2, []))->toBeInstanceOf(ShouldBroadcastNow::class);
});

it('broadcasts on the conversation private channel', function () {
    $event = new MessagesRead(conversationId: 42, readerId: 7, messageIds: [1, 2, 3]);

    $channels = $event->broadcastOn();

    expect($channels)->toHaveCount(1)
        ->and($channels[0])->toBeInstanceOf(PrivateChannel::class)
        ->and($channels[0]->name)->toBe('private-conversation.42');
});

it('broadcasts a payload with conversation, reader and message ids', function () {
    $event = new MessagesRead(conversationId: 42, readerId: 7, messageIds: [1, 2, 3]);

    $payload = $event->broadcastWith();

    expect($payload)
        ->toHaveKeys(['conversation_id', 'reader_id', 'message_ids', 'read_at'])
        ->and($payload['conversation_id'])->toBe(42)
        ->and($payload['reader_id'])->toBe(7)
        ->and($payload['message_ids'])->toBe([1, 2, 3]);
});

it('uses messages.read as the broadcast name', function () {
    expect(new MessagesRead(1, 2, [])->broadcastAs())->toBe('messages.read');
});
