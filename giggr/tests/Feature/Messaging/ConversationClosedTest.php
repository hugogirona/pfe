<?php

use App\Enums\ContactPreference;
use App\Events\ConversationClosed;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;

it('broadcasts ConversationClosed when blocking a user with an existing conversation', function () {
    Event::fake([ConversationClosed::class]);
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $conversation = Conversation::between($alice, $bob);

    $bob->block($alice);

    Event::assertDispatched(
        ConversationClosed::class,
        fn (ConversationClosed $e) => $e->conversationId === $conversation->id,
    );
});

it('does not broadcast when blocking a user with no conversation', function () {
    Event::fake([ConversationClosed::class]);
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();

    $bob->block($alice);

    Event::assertNotDispatched(ConversationClosed::class);
});

it('broadcasts ConversationClosed to pending requesters when closing contact', function () {
    Event::fake([ConversationClosed::class]);
    $owner = User::factory()->withProfile()->create();
    $requester = User::factory()->withProfile()->create();
    $conversation = Conversation::between($requester, $owner); // pending, requester reaches owner

    Livewire::actingAs($owner)
        ->test('parts.settings.privacy')
        ->set('contactPreference', ContactPreference::Nobody->value);

    Event::assertDispatched(
        ConversationClosed::class,
        fn (ConversationClosed $e) => $e->conversationId === $conversation->id,
    );
});

it('locks a pending conversation the owner started when closing contact', function () {
    Event::fake([ConversationClosed::class]);
    $owner = User::factory()->withProfile()->create();
    $other = User::factory()->withProfile()->create();
    $conversation = Conversation::between($owner, $other); // owner is the requester

    Livewire::actingAs($owner)
        ->test('parts.settings.privacy')
        ->set('contactPreference', ContactPreference::Nobody->value);

    Event::assertDispatched(
        ConversationClosed::class,
        fn (ConversationClosed $e) => $e->conversationId === $conversation->id,
    );
});

it('does not lock an already-accepted conversation when closing contact', function () {
    Event::fake([ConversationClosed::class]);
    $owner = User::factory()->withProfile()->create();
    $requester = User::factory()->withProfile()->create();
    $conversation = Conversation::between($requester, $owner);
    $conversation->update(['accepted_at' => now()]);

    Livewire::actingAs($owner)
        ->test('parts.settings.privacy')
        ->set('contactPreference', ContactPreference::Nobody->value);

    Event::assertNotDispatched(ConversationClosed::class);
});
