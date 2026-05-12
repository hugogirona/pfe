<?php

use App\Actions\SendMessage;
use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;

it('sends a message and creates the conversation on first send', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();

    $message = app(SendMessage::class)->execute($alice, $bob, 'Salut !');

    expect($message)->toBeInstanceOf(Message::class)
        ->and($message->body)->toBe('Salut !')
        ->and($message->sender_id)->toBe($alice->id)
        ->and(Conversation::count())->toBe(1);
});

it('reuses the existing conversation on subsequent messages', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $action = app(SendMessage::class);

    $action->execute($alice, $bob, 'First');
    $action->execute($bob, $alice, 'Reply');
    $action->execute($alice, $bob, 'Third');

    expect(Conversation::count())->toBe(1)
        ->and(Message::count())->toBe(3);
});

it('records the first sender as the conversation requester', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();

    app(SendMessage::class)->execute($alice, $bob, 'Hello');

    expect(Conversation::first()->requester_user_id)->toBe($alice->id);
});

it('rejects sending a message to yourself', function () {
    $alice = User::factory()->withProfile()->create();

    expect(fn () => app(SendMessage::class)->execute($alice, $alice, 'Hi'))
        ->toThrow(InvalidArgumentException::class);
});

it('rejects an empty body', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();

    expect(fn () => app(SendMessage::class)->execute($alice, $bob, ''))
        ->toThrow(ValidationException::class);
});

it('rejects a body longer than 2000 characters', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();

    expect(fn () => app(SendMessage::class)->execute($alice, $bob, str_repeat('a', 2001)))
        ->toThrow(ValidationException::class);
});

it('auto-accepts when the recipient already follows the sender profile', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $bob->follow($alice->profile);

    app(SendMessage::class)->execute($alice, $bob, 'Hey');

    expect(Conversation::first()->accepted_at)->not->toBeNull();
});

it('auto-accepts when the recipient replies to a pending conversation', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $action = app(SendMessage::class);

    $action->execute($alice, $bob, 'Hi');
    expect(Conversation::first()->accepted_at)->toBeNull();

    $action->execute($bob, $alice, 'Hey there');

    expect(Conversation::first()->fresh()->accepted_at)->not->toBeNull();
});

it('stays pending when neither auto-accept condition is met', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $action = app(SendMessage::class);

    $action->execute($alice, $bob, 'Hi');
    $action->execute($alice, $bob, 'Hi again');

    expect(Conversation::first()->accepted_at)->toBeNull();
});

it('touches last_message_at on every send', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();

    app(SendMessage::class)->execute($alice, $bob, 'Hi');

    expect(Conversation::first()->last_message_at)->not->toBeNull();
});

it('dispatches MessageSent after sending', function () {
    Event::fake();
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();

    $message = app(SendMessage::class)->execute($alice, $bob, 'Hi');

    Event::assertDispatched(MessageSent::class, fn (MessageSent $event) => $event->message->id === $message->id);
});

it('unhides the conversation for the recipient on new message', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $action = app(SendMessage::class);

    $action->execute($alice, $bob, 'Hi');
    $convoId = Conversation::first()->id;
    $bob->conversations()->updateExistingPivot($convoId, ['hidden_at' => now()]);
    expect($bob->conversations()->first()->pivot->hidden_at)->not->toBeNull();

    $action->execute($alice, $bob, 'Hi again');

    expect($bob->fresh()->conversations()->first()->pivot->hidden_at)->toBeNull();
});
