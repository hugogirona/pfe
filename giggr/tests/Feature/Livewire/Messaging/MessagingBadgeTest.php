<?php

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Livewire\Livewire;

it('renders nothing visible when the user has no unread messages', function () {
    $alice = User::factory()->withProfile()->create();

    Livewire::actingAs($alice)
        ->test('parts.layout.messaging-badge')
        ->assertSet('count', 0)
        ->assertDontSeeHtml('bg-accent text-on-dark');
});

it('shows the number of conversations with unread messages on mount', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $charlie = User::factory()->withProfile()->create();

    $aliceBob = Conversation::between($alice, $bob);
    $aliceCharlie = Conversation::between($alice, $charlie);
    Message::factory()->for($aliceBob)->for($bob, 'sender')->count(5)->create(['read_at' => null]);
    Message::factory()->for($aliceCharlie)->for($charlie, 'sender')->count(1)->create(['read_at' => null]);

    Livewire::actingAs($alice)
        ->test('parts.layout.messaging-badge')
        ->assertSet('count', 2)
        ->assertSee('2');
});

it('counts a single conversation once even with many unread messages', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);
    Message::factory()->for($convo)->for($bob, 'sender')->count(10)->create(['read_at' => null]);

    Livewire::actingAs($alice)
        ->test('parts.layout.messaging-badge')
        ->assertSet('count', 1)
        ->assertSee('1');
});

it('refreshes the count on messaging-updated event', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);

    $component = Livewire::actingAs($alice)
        ->test('parts.layout.messaging-badge')
        ->assertSet('count', 0);

    Message::factory()->for($convo)->for($bob, 'sender')->count(3)->create(['read_at' => null]);

    $component->dispatch('messaging-updated')->assertSet('count', 1);
});

it('caps the displayed value at 99 plus', function () {
    $alice = User::factory()->withProfile()->create();

    // 100 conversations from 100 distinct senders, each with one unread message.
    for ($i = 0; $i < 100; $i++) {
        $sender = User::factory()->withProfile()->create();
        $convo = Conversation::between($alice, $sender);
        Message::factory()->for($convo)->for($sender, 'sender')->create(['read_at' => null]);
    }

    Livewire::actingAs($alice)
        ->test('parts.layout.messaging-badge')
        ->assertSet('count', 100)
        ->assertSee('99+');
});

it('guests do not mount the badge', function () {
    Livewire::test('parts.layout.messaging-badge')
        ->assertForbidden();
});
