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

it('openConversation marks unread messages from others as read', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $unreadFromBob = Message::factory()->for($convo)->for($bob, 'sender')->count(3)->create(['read_at' => null]);
    $ownMessage = Message::factory()->for($convo)->for($alice, 'sender')->create(['read_at' => null]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id);

    foreach ($unreadFromBob as $message) {
        expect($message->fresh()->read_at)->not->toBeNull();
    }
    expect($ownMessage->fresh()->read_at)->toBeNull();
});

it('messageReceived marks the new incoming message as read', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);

    $component = Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id);

    $incoming = Message::factory()->for($convo)->for($bob, 'sender')->create(['read_at' => null]);

    $component->call('messageReceived', ['conversation_id' => $convo->id, 'id' => $incoming->id]);

    expect($incoming->fresh()->read_at)->not->toBeNull();
});

it('incomingMessage refreshes the conversation list with fresh unread counts', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now(), 'last_message_at' => now()]);

    $component = Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->assertDontSeeHtml('bg-accent text-bg text-[10px]');

    Message::factory()->for($convo)->for($bob, 'sender')->create([
        'body' => 'Live message',
        'read_at' => null,
    ]);

    $component
        ->call('incomingMessage', ['conversation_id' => $convo->id])
        ->assertSee('Live message')
        ->assertSeeHtml('bg-accent text-bg text-[10px]');
});

it('shows the unread count in the conversation row when messages are unread', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now(), 'last_message_at' => now()]);
    Message::factory()->for($convo)->for($bob, 'sender')->count(4)->create(['read_at' => null]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->assertSee('4')
        ->assertSee($bob->full_name);
});

it('does not show an unread count when the conversation is fully read', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now(), 'last_message_at' => now()]);
    Message::factory()->for($convo)->for($bob, 'sender')->count(3)->create(['read_at' => now()]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->assertDontSeeHtml('bg-accent text-bg text-[10px]');
});

it('renders the time HH:MM on each message bubble in the thread', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now()]);
    Message::factory()->for($convo)->for($bob, 'sender')->create([
        'body' => 'Bonjour',
        'created_at' => now()->setTime(14, 32),
    ]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id)
        ->assertSee('14:32');
});

it('sets the new-messages marker when opening a conversation with unread incoming', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now()]);

    Message::factory()->for($convo)->for($alice, 'sender')->create(['body' => 'old mine', 'read_at' => null]);
    $firstUnread = Message::factory()->for($convo)->for($bob, 'sender')->create(['body' => 'unread 1', 'read_at' => null]);
    Message::factory()->for($convo)->for($bob, 'sender')->count(2)->create(['body' => 'unread n', 'read_at' => null]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id)
        ->assertSet('newMessageMarkerId', $firstUnread->id)
        ->assertSet('newMessageMarkerCount', 3);
});

it('does not set the new-messages marker when there are no unread incoming', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now()]);
    Message::factory()->for($convo)->for($bob, 'sender')->create(['body' => 'read', 'read_at' => now()]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id)
        ->assertSet('newMessageMarkerId', null)
        ->assertSet('newMessageMarkerCount', 0);
});

it('dismissNewMessageMarker clears the marker state', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now()]);
    Message::factory()->for($convo)->for($bob, 'sender')->create(['read_at' => null]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id)
        ->call('dismissNewMessageMarker')
        ->assertSet('newMessageMarkerId', null)
        ->assertSet('newMessageMarkerCount', 0);
});

it('backToList clears the new-messages marker', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now()]);
    Message::factory()->for($convo)->for($bob, 'sender')->create(['read_at' => null]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id)
        ->call('backToList')
        ->assertSet('newMessageMarkerId', null);
});

it('inserts a day separator between messages from different days', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now()]);
    Message::factory()->for($convo)->for($bob, 'sender')->create([
        'body' => 'Old message',
        'created_at' => now()->subDay(),
    ]);
    Message::factory()->for($convo)->for($alice, 'sender')->create([
        'body' => 'Today reply',
        'created_at' => now(),
    ]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id)
        ->assertSee(__('messaging.day_yesterday'))
        ->assertSee(__('messaging.day_today'));
});

it('readReceiptReceived in thread view busts the conversation cache', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now()]);
    $myMessage = Message::factory()->for($convo)->for($alice, 'sender')->create([
        'body' => 'Salut Bob',
        'read_at' => null,
    ]);

    $component = Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id);

    // Bob reads the message in the background
    $myMessage->update(['read_at' => now()]);

    $component
        ->call('readReceiptReceived', ['conversation_id' => $convo->id])
        ->assertSee('Salut Bob');

    expect($component->get('currentConversation')->messages->first()->read_at)->not->toBeNull();
});

it('readReceiptReceived is a no-op when not viewing the matching conversation', function () {
    $alice = User::factory()->withProfile()->create();

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('readReceiptReceived', ['conversation_id' => 999])
        ->assertSet('view', 'list');
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

it('acceptRequest sets accepted_at and moves convo to messages tab', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $request = Conversation::between($bob, $alice);
    $request->update(['last_message_at' => now()]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('acceptRequest', $request->id);

    expect($request->fresh()->accepted_at)->not->toBeNull();
});

it('acceptRequest refuses when called by the requester', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $request = Conversation::between($alice, $bob);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('acceptRequest', $request->id)
        ->assertForbidden();

    expect($request->fresh()->accepted_at)->toBeNull();
});

it('acceptRequest refuses when called by an outsider', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $eve = User::factory()->withProfile()->create();
    $request = Conversation::between($bob, $alice);

    Livewire::actingAs($eve)
        ->test('parts.messaging.inbox')
        ->call('acceptRequest', $request->id)
        ->assertForbidden();
});

it('acceptRequest is a no-op if convo is already accepted', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $request = Conversation::between($bob, $alice);
    $originalAcceptedAt = now()->subHour()->startOfSecond();
    $request->update(['accepted_at' => $originalAcceptedAt]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('acceptRequest', $request->id);

    expect($request->fresh()->accepted_at->getTimestamp())->toBe($originalAcceptedAt->getTimestamp());
});

it('declineRequest hides the conversation for the current user', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $request = Conversation::between($bob, $alice);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('declineRequest', $request->id);

    $pivot = $alice->fresh()->conversations()->find($request->id)->pivot;
    expect($pivot->hidden_at)->not->toBeNull();
});

it('declineRequest leaves the conversation visible for the other participant', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $request = Conversation::between($bob, $alice);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('declineRequest', $request->id);

    $bobPivot = $bob->fresh()->conversations()->find($request->id)->pivot;
    expect($bobPivot->hidden_at)->toBeNull();
});

it('declineRequest refuses when called by the requester', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $request = Conversation::between($alice, $bob);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('declineRequest', $request->id)
        ->assertForbidden();
});

it('declineRequest refuses when called by an outsider', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $eve = User::factory()->withProfile()->create();
    $request = Conversation::between($bob, $alice);

    Livewire::actingAs($eve)
        ->test('parts.messaging.inbox')
        ->call('declineRequest', $request->id)
        ->assertForbidden();
});

it('declineRequest closes the thread and returns to list', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $request = Conversation::between($bob, $alice);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $request->id)
        ->call('declineRequest', $request->id)
        ->assertSet('view', 'list')
        ->assertSet('currentConversationId', null);
});

it('thread view shows blocked-by-you notice when current user has blocked the correspondent', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now()]);
    $alice->block($bob);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id)
        ->assertSee(__('messaging.blocked_by_you'))
        ->assertDontSee(__('messaging.compose_placeholder'));
});

it('thread view shows blocked-by-them notice when correspondent has blocked current user', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now()]);
    $bob->block($alice);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id)
        ->assertSee(__('messaging.blocked_by_them'))
        ->assertDontSee(__('messaging.compose_placeholder'));
});

it('thread view shows the compose form when not blocked', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now()]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id)
        ->assertSee(__('messaging.compose_placeholder'))
        ->assertDontSee(__('messaging.blocked_by_you'))
        ->assertDontSee(__('messaging.blocked_by_them'));
});

it('deleteConversation hides the conversation for the current user', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now()]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id)
        ->call('deleteConversation', $convo->id);

    $pivot = $alice->fresh()->conversations()->find($convo->id)->pivot;
    expect($pivot->hidden_at)->not->toBeNull()
        ->and($bob->fresh()->conversations()->find($convo->id)->pivot->hidden_at)->toBeNull();
});

it('deleteConversation returns to the list view and hides the row', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now(), 'last_message_at' => now()]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id)
        ->call('deleteConversation', $convo->id)
        ->assertSet('view', 'list')
        ->assertDontSee($bob->full_name);
});

it('deleteConversation aborts when the user does not participate in the conversation', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $carol = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);

    Livewire::actingAs($carol)
        ->test('parts.messaging.inbox')
        ->call('deleteConversation', $convo->id)
        ->assertStatus(403);
});

it('thread view shows the delete conversation button on accepted conversations', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now()]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id)
        ->assertSee(__('messaging.delete_conversation_aria', ['name' => $bob->full_name]));
});

it('blockCorrespondent blocks the other participant and returns to the list', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now()]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id)
        ->call('blockCorrespondent')
        ->assertSet('view', 'list');

    expect($alice->fresh()->hasBlocked($bob))->toBeTrue()
        ->and($alice->fresh()->conversations()->find($convo->id)->pivot->hidden_at)->not->toBeNull();
});

it('blockCorrespondent aborts when not in a thread view', function () {
    $alice = User::factory()->withProfile()->create();

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('blockCorrespondent')
        ->assertStatus(403);
});

it('thread view shows the block correspondent button on accepted conversations', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    $convo->update(['accepted_at' => now()]);

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('openConversation', $convo->id)
        ->assertSee(__('messaging.block_correspondent_aria', ['name' => $bob->full_name]));
});

it('switchTab ignores invalid values', function () {
    $alice = User::factory()->withProfile()->create();

    Livewire::actingAs($alice)
        ->test('parts.messaging.inbox')
        ->call('switchTab', 'unknown')
        ->assertSet('activeTab', 'messages');
});
