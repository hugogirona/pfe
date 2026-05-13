<?php

use App\Actions\MarkConversationAsRead;
use App\Events\MessagesRead;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Event;

it('sets read_at on all unread incoming messages', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $unread = Message::factory()->for($convo)->for($bob, 'sender')->count(3)->create(['read_at' => null]);

    app(MarkConversationAsRead::class)->execute($alice, $convo->id);

    foreach ($unread as $message) {
        expect($message->fresh()->read_at)->not->toBeNull();
    }
});

it('leaves the reader own messages untouched', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $mine = Message::factory()->for($convo)->for($alice, 'sender')->create(['read_at' => null]);

    app(MarkConversationAsRead::class)->execute($alice, $convo->id);

    expect($mine->fresh()->read_at)->toBeNull();
});

it('leaves already-read messages untouched', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $previousReadAt = now()->subHour()->startOfSecond();
    $already = Message::factory()->for($convo)->for($bob, 'sender')->create(['read_at' => $previousReadAt]);

    app(MarkConversationAsRead::class)->execute($alice, $convo->id);

    expect($already->fresh()->read_at->getTimestamp())->toBe($previousReadAt->getTimestamp());
});

it('touches the reader pivot last_read_at', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);

    app(MarkConversationAsRead::class)->execute($alice, $convo->id);

    $pivot = $alice->fresh()->conversations()->find($convo->id)->pivot;
    expect($pivot->last_read_at)->not->toBeNull();
});

it('broadcasts MessagesRead with the freshly read message ids', function () {
    Event::fake();
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $unread = Message::factory()->for($convo)->for($bob, 'sender')->count(2)->create(['read_at' => null]);

    app(MarkConversationAsRead::class)->execute($alice, $convo->id);

    Event::assertDispatched(MessagesRead::class, function (MessagesRead $event) use ($convo, $alice, $unread) {
        return $event->conversationId === $convo->id
            && $event->readerId === $alice->id
            && count($event->messageIds) === 2
            && $unread->every(fn ($m) => in_array($m->id, $event->messageIds, true));
    });
});

it('does not broadcast MessagesRead when nothing was unread', function () {
    Event::fake();
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);

    app(MarkConversationAsRead::class)->execute($alice, $convo->id);

    Event::assertNotDispatched(MessagesRead::class);
});
