<?php

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;

it('returns 0 when the user has no unread conversations', function () {
    $alice = User::factory()->withProfile()->create();

    expect($alice->unreadConversationsCount())->toBe(0);
});

it('counts a conversation once regardless of how many unread messages it holds', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    Message::factory()->for($convo)->for($bob, 'sender')->count(10)->create(['read_at' => null]);

    expect($alice->unreadConversationsCount())->toBe(1);
});

it('counts each distinct conversation that has unread messages', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $charlie = User::factory()->withProfile()->create();

    $aliceBob = Conversation::between($alice, $bob);
    $aliceCharlie = Conversation::between($alice, $charlie);

    Message::factory()->for($aliceBob)->for($bob, 'sender')->count(3)->create(['read_at' => null]);
    Message::factory()->for($aliceCharlie)->for($charlie, 'sender')->count(2)->create(['read_at' => null]);

    expect($alice->unreadConversationsCount())->toBe(2);
});

it('ignores conversations where only the user has sent messages', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);

    Message::factory()->for($convo)->for($alice, 'sender')->count(4)->create(['read_at' => null]);

    expect($alice->unreadConversationsCount())->toBe(0);
});

it('ignores conversations where all incoming messages are read', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);

    Message::factory()->for($convo)->for($bob, 'sender')->count(3)->create(['read_at' => now()]);

    expect($alice->unreadConversationsCount())->toBe(0);
});

it('ignores conversations hidden by the user', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $charlie = User::factory()->withProfile()->create();

    $visible = Conversation::between($alice, $bob);
    $hidden = Conversation::between($alice, $charlie);
    $alice->conversations()->updateExistingPivot($hidden->id, ['hidden_at' => now()]);

    Message::factory()->for($visible)->for($bob, 'sender')->create(['read_at' => null]);
    Message::factory()->for($hidden)->for($charlie, 'sender')->create(['read_at' => null]);

    expect($alice->unreadConversationsCount())->toBe(1);
});

it('ignores conversations the user is not part of', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $charlie = User::factory()->withProfile()->create();
    $bobCharlie = Conversation::between($bob, $charlie);

    Message::factory()->for($bobCharlie)->for($bob, 'sender')->create(['read_at' => null]);

    expect($alice->unreadConversationsCount())->toBe(0);
});
