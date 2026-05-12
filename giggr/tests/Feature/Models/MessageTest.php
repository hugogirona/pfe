<?php

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Carbon;

it('belongs to a conversation', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    $convo = Conversation::between($alice, $bob);
    $message = Message::factory()->for($convo)->for($alice, 'sender')->create();

    expect($message->conversation)->not->toBeNull()
        ->and($message->conversation->id)->toBe($convo->id);
});

it('belongs to a sender', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    $convo = Conversation::between($alice, $bob);
    $message = Message::factory()->for($convo)->for($alice, 'sender')->create();

    expect($message->sender)->not->toBeNull()
        ->and($message->sender->id)->toBe($alice->id);
});

it('casts read_at to datetime', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    $convo = Conversation::between($alice, $bob);
    $message = Message::factory()->for($convo)->for($alice, 'sender')->create(['read_at' => now()]);

    expect($message->read_at)->toBeInstanceOf(Carbon::class);
});

it('body is fillable and persisted', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    $convo = Conversation::between($alice, $bob);

    $message = Message::create([
        'conversation_id' => $convo->id,
        'sender_id' => $alice->id,
        'body' => 'Salut !',
    ]);

    expect($message->fresh()->body)->toBe('Salut !');
});

it('is removed when its conversation is deleted', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    $convo = Conversation::between($alice, $bob);
    $message = Message::factory()->for($convo)->for($alice, 'sender')->create();

    $convo->delete();

    expect(Message::find($message->id))->toBeNull();
});
