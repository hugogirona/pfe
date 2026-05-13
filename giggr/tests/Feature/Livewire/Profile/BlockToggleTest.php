<?php

use App\Models\User;
use Livewire\Livewire;

it('mounts as not blocked by default', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();

    Livewire::actingAs($alice)
        ->test('parts.profile.block-toggle', ['targetUserId' => $bob->id])
        ->assertSet('isBlocked', false);
});

it('mounts as blocked when already blocked', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $alice->block($bob);

    Livewire::actingAs($alice)
        ->test('parts.profile.block-toggle', ['targetUserId' => $bob->id])
        ->assertSet('isBlocked', true);
});

it('toggle blocks the user', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();

    Livewire::actingAs($alice)
        ->test('parts.profile.block-toggle', ['targetUserId' => $bob->id])
        ->call('toggle')
        ->assertSet('isBlocked', true);

    expect($alice->fresh()->hasBlocked($bob))->toBeTrue();
});

it('toggle unblocks the user', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $alice->block($bob);

    Livewire::actingAs($alice)
        ->test('parts.profile.block-toggle', ['targetUserId' => $bob->id])
        ->call('toggle')
        ->assertSet('isBlocked', false);

    expect($alice->fresh()->hasBlocked($bob))->toBeFalse();
});

it('refuses to mount on self', function () {
    $alice = User::factory()->withProfile()->create();

    Livewire::actingAs($alice)
        ->test('parts.profile.block-toggle', ['targetUserId' => $alice->id])
        ->assertForbidden();
});

it('guest cannot mount', function () {
    $bob = User::factory()->withProfile()->create();

    Livewire::test('parts.profile.block-toggle', ['targetUserId' => $bob->id])
        ->assertForbidden();
});
