<?php

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Livewire\Livewire;

it('lists accepted conversations in the messages tab by default', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $accepted = Conversation::between($alice, $bob);
    $accepted->update(['accepted_at' => now(), 'last_message_at' => now()]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->assertSet('view', 'list')
        ->assertSet('activeTab', 'messages')
        ->assertSee($bob->full_name);
});

it('shows pending requests received in the requests tab', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $request = Conversation::between($bob, $alice);
    $request->update(['last_message_at' => now()]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('switchTab', 'requests')
        ->assertSet('activeTab', 'requests')
        ->assertSee($bob->full_name);
});

it('does not show outbound pending conversations in the requests tab', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $outbound = Conversation::between($alice, $bob);
    $outbound->update(['last_message_at' => now()]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('switchTab', 'requests')
        ->assertDontSee($bob->full_name);
});

it('shows outbound pending conversations in the messages tab', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $outbound = Conversation::between($alice, $bob);
    $outbound->update(['last_message_at' => now()]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->assertSee($bob->full_name);
});

it('hides conversations the current user has hidden', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now(), 'last_message_at' => now()]);
    $alice->conversations()->updateExistingPivot($convo->id, ['hidden_at' => now()]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->assertDontSee($bob->full_name);
});

it('opens a conversation, marks it as read and switches to thread view', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now()]);
    Message::factory()->for($convo)->for($bob, 'sender')->create(['body' => 'Coucou']);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id)
        ->assertSet('view', 'thread')
        ->assertSet('currentConversationId', $convo->id)
        ->assertSee('Coucou');

    $pivot = $alice->conversations()->find($convo->id)->pivot;
    expect($pivot->last_read_at)->not->toBeNull();
});

it('refuses to open a conversation the user is not part of', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $eve = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);

    Livewire::actingAs($eve)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id)
        ->assertForbidden();
});

it('back to list clears state', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now()]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id)
        ->call('backToList')
        ->assertSet('view', 'list')
        ->assertSet('currentConversationId', null)
        ->assertSet('body', '');
});

it('sends a message in the thread view via SendMessage', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now()]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id)
        ->set('body', 'Salut Bob !')
        ->call('send')
        ->assertSet('body', '')
        ->assertSee('Salut Bob !');

    expect(Message::where('conversation_id', $convo->id)->where('body', 'Salut Bob !')->exists())
        ->toBeTrue();
});

it('rejects sending outside of thread view', function () {
    $alice = User::factory()->withProfile()->create();

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->set('body', 'Salut')
        ->call('send')
        ->assertForbidden();
});

it('guests cannot mount the inbox', function () {
    Livewire::test('parts.messaging.inbox')
        ->assertForbidden();
});

it('mount with model_id opens an existing conversation in thread view', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now()]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox', ['model_id' => $bob->id])
        ->assertSet('view', 'thread')
        ->assertSet('currentConversationId', $convo->id)
        ->assertSet('draftRecipientId', null);
});

it('mount with model_id of a stranger opens a draft thread without a Conversation row', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox', ['model_id' => $bob->id])
        ->assertSet('view', 'thread')
        ->assertSet('currentConversationId', null)
        ->assertSet('draftRecipientId', $bob->id)
        ->assertSee($bob->full_name);

    expect(Conversation::count())->toBe(0);
});

it('sending from a draft thread creates the conversation lazily', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox', ['model_id' => $bob->id])
        ->set('body', 'Premier message')
        ->call('send')
        ->assertSet('body', '')
        ->assertSet('draftRecipientId', null)
        ->assertSee('Premier message');

    expect(Conversation::count())->toBe(1)
        ->and(Message::where('body', 'Premier message')->exists())->toBeTrue();
});

it('mount ignores model_id pointing at self', function () {
    $alice = User::factory()->withProfile()->create();

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox', ['model_id' => $alice->id])
        ->assertSet('view', 'list')
        ->assertSet('currentConversationId', null)
        ->assertSet('draftRecipientId', null);
});

it('mount ignores model_id pointing at an unknown user', function () {
    $alice = User::factory()->withProfile()->create();

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox', ['model_id' => 999999])
        ->assertSet('view', 'list')
        ->assertSet('draftRecipientId', null);
});

it('messageReceived marks the conversation as read when viewing it', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now()]);

    $component = Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id);

    $alice->conversations()->updateExistingPivot($convo->id, ['last_read_at' => now()->subHour()]);

    $component->call('messageReceived', ['conversation_id' => $convo->id, 'id' => 1, 'body' => 'New']);

    $pivot = $alice->fresh()->conversations()->find($convo->id)->pivot;
    expect($pivot->last_read_at)->toBeGreaterThan(now()->subMinute());
});

it('messageReceived refreshes the thread to show the new message', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now()]);

    $component = Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id)
        ->assertDontSee('Live message from Bob');

    Message::factory()
        ->for($convo)
        ->for($bob, 'sender')
        ->create(['body' => 'Live message from Bob']);

    $component
        ->call('messageReceived', ['conversation_id' => $convo->id])
        ->assertSee('Live message from Bob');
});

it('messageReceived is a no-op in list view', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('messageReceived', ['conversation_id' => $convo->id, 'id' => 1])
        ->assertSet('view', 'list');
});

it('messageReceived ignores payloads for a different conversation', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $charlie = User::factory()->withProfile()->create();
    $aliceBob = Conversation::between($alice, $bob);
    $aliceBob->update(['accepted_at' => now()]);
    $aliceCharlie = Conversation::between($alice, $charlie);

    $alice->conversations()->updateExistingPivot($aliceCharlie->id, ['last_read_at' => now()->subHour()]);
    $charlieReadAtBefore = $alice->conversations()->find($aliceCharlie->id)->pivot->last_read_at;

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $aliceBob->id)
        ->call('messageReceived', ['conversation_id' => $aliceCharlie->id, 'id' => 1]);

    $pivotAfter = $alice->fresh()->conversations()->find($aliceCharlie->id)->pivot;
    expect((string) $pivotAfter->last_read_at)->toBe((string) $charlieReadAtBefore);
});

it('switchTab ignores invalid values', function () {
    $alice = User::factory()->withProfile()->create();

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('switchTab', 'unknown')
        ->assertSet('activeTab', 'messages');
});
