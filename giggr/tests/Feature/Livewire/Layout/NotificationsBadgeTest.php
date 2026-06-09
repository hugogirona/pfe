<?php

use App\Models\User;
use App\Notifications\NewFollower;
use Livewire\Livewire;

it('starts with no unread notifications', function () {
    $user = User::factory()->withProfile()->create();

    Livewire::actingAs($user)
        ->test('parts.layout.notifications-badge')
        ->assertSet('count', 0);
});

it('reflects the unread notification count', function () {
    $user = User::factory()->withProfile()->create();
    $follower = User::factory()->withProfile()->create();
    $user->notify(new NewFollower($follower));

    Livewire::actingAs($user)
        ->test('parts.layout.notifications-badge')
        ->assertSet('count', 1);
});

it('refreshes the count on the notifications-updated event', function () {
    $user = User::factory()->withProfile()->create();
    $follower = User::factory()->withProfile()->create();

    $component = Livewire::actingAs($user)
        ->test('parts.layout.notifications-badge')
        ->assertSet('count', 0);

    $user->notify(new NewFollower($follower));

    $component->dispatch('notifications-updated')->assertSet('count', 1);
});
