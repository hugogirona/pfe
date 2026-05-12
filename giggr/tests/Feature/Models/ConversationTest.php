<?php

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Carbon;

it('between() returns the same row regardless of arg order', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();

    $first = Conversation::between($alice, $bob);
    $second = Conversation::between($bob, $alice);

    expect($first->id)->toBe($second->id)
        ->and(Conversation::count())->toBe(1);
});

it('between() stores the pair with user_a_id smaller than user_b_id', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    [$low, $high] = $alice->id < $bob->id ? [$alice, $bob] : [$bob, $alice];

    $convo = Conversation::between($high, $low);

    expect($convo->user_a_id)->toBe($low->id)
        ->and($convo->user_b_id)->toBe($high->id);
});

it('between() records the requester as the first caller', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();

    $convo = Conversation::between($alice, $bob);

    expect($convo->requester_user_id)->toBe($alice->id);
});

it('between() seeds a pivot row for each participant', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();

    $convo = Conversation::between($alice, $bob);

    expect($convo->participants()->pluck('users.id')->all())
        ->toContain($alice->id)
        ->toContain($bob->id);
});

it('refuses to create a conversation between a user and themself', function () {
    $alice = User::factory()->create();

    expect(fn () => Conversation::between($alice, $alice))
        ->toThrow(InvalidArgumentException::class);
});

it('hasParticipant() returns true for members and false for outsiders', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    $eve = User::factory()->create();
    $convo = Conversation::between($alice, $bob);

    expect($convo->hasParticipant($alice))->toBeTrue()
        ->and($convo->hasParticipant($bob))->toBeTrue()
        ->and($convo->hasParticipant($eve))->toBeFalse();
});

it('messages() returns the conversation messages ordered by creation', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    $convo = Conversation::between($alice, $bob);

    Message::factory()->for($convo)->for($alice, 'sender')->create(['body' => 'Hi']);
    Message::factory()->for($convo)->for($bob, 'sender')->create(['body' => 'Hello']);

    expect($convo->messages()->pluck('body')->all())->toBe(['Hi', 'Hello']);
});

it('requester() returns the user who started the conversation', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    $convo = Conversation::between($alice, $bob);

    expect($convo->requester)->not->toBeNull()
        ->and($convo->requester->id)->toBe($alice->id);
});

it('casts accepted_at and last_message_at to datetime', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now(), 'last_message_at' => now()]);

    expect($convo->accepted_at)->toBeInstanceOf(Carbon::class)
        ->and($convo->last_message_at)->toBeInstanceOf(Carbon::class);
});
