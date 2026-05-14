<?php

use App\Actions\HideConversation;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;

it('hides the conversation by setting hidden_at on the user pivot', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);

    app(HideConversation::class)->execute($alice, $convo->id);

    $pivot = $alice->fresh()->conversations()->find($convo->id)->pivot;
    expect($pivot->hidden_at)->not->toBeNull();
});

it('does not affect the other participant pivot', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);

    app(HideConversation::class)->execute($alice, $convo->id);

    $bobPivot = $bob->fresh()->conversations()->find($convo->id)->pivot;
    expect($bobPivot->hidden_at)->toBeNull();
});

it('is idempotent when called twice', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $action = app(HideConversation::class);

    $action->execute($alice, $convo->id);
    $action->execute($alice, $convo->id);

    $pivot = $alice->fresh()->conversations()->find($convo->id)->pivot;
    expect($pivot->hidden_at)->not->toBeNull();
});

it('does not delete messages', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    Message::factory()->for($convo)->for($alice, 'sender')->count(3)->create();

    app(HideConversation::class)->execute($alice, $convo->id);

    expect(Message::where('conversation_id', $convo->id)->count())->toBe(3);
});

it('throws when the user does not participate in the conversation', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $carol = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);

    expect(fn () => app(HideConversation::class)->execute($carol, $convo->id))
        ->toThrow(InvalidArgumentException::class);
});
