<?php

use App\Models\User;
use Database\Seeders\CitySeeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\InstrumentSeeder;
use Livewire\Livewire;

it('settings account page redirects guests to login', function () {
    $this->get(route('settings.account'))->assertRedirectToRoute('login');
});

it('settings account page renders the empty state when no blocks', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $user = User::factory()->withProfile()->create();

    $this->actingAs($user)
        ->get(route('settings.account'))
        ->assertOk()
        ->assertSee(__('settings.blocked_users_empty'));
});

it('settings account page lists the blocked users', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $charlie = User::factory()->withProfile()->create();
    $alice->block($bob);
    $alice->block($charlie);

    $this->actingAs($alice)
        ->get(route('settings.account'))
        ->assertOk()
        ->assertSee($bob->full_name)
        ->assertSee($charlie->full_name);
});

it('toggleBlock unblocks an initially blocked user', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $alice->block($bob);

    Livewire::actingAs($alice)
        ->test('pages::settings.account')
        ->call('toggleBlock', $bob->id);

    expect($alice->fresh()->hasBlocked($bob))->toBeFalse();
});

it('toggleBlock keeps the row visible after unblock', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $alice->block($bob);

    Livewire::actingAs($alice)
        ->test('pages::settings.account')
        ->call('toggleBlock', $bob->id)
        ->assertSee($bob->full_name)
        ->assertSee(__('settings.block'));
});

it('toggleBlock can re-block the user from the same row', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $alice->block($bob);

    Livewire::actingAs($alice)
        ->test('pages::settings.account')
        ->call('toggleBlock', $bob->id)
        ->call('toggleBlock', $bob->id);

    expect($alice->fresh()->hasBlocked($bob))->toBeTrue();
});

it('reloading the settings page drops unblocked rows', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $alice->block($bob);
    $alice->unblock($bob);

    $this->actingAs($alice)
        ->get(route('settings.account'))
        ->assertOk()
        ->assertDontSee($bob->full_name);
});

it('toggleBlock with a user id outside of the initial snapshot is forbidden', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();

    Livewire::actingAs($alice)
        ->test('pages::settings.account')
        ->call('toggleBlock', $bob->id)
        ->assertForbidden();
});

it('guest cannot mount the settings page Livewire component', function () {
    Livewire::test('pages::settings.account')->assertForbidden();
});
