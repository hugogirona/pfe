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

it('unblock action removes the block', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $alice->block($bob);

    Livewire::actingAs($alice)
        ->test('pages::settings.account')
        ->call('unblock', $bob->id);

    expect($alice->fresh()->hasBlocked($bob))->toBeFalse();
});

it('unblock with unknown user id is a no-op', function () {
    $alice = User::factory()->withProfile()->create();

    Livewire::actingAs($alice)
        ->test('pages::settings.account')
        ->call('unblock', 999999)
        ->assertOk();
});

it('guest cannot mount the settings page Livewire component', function () {
    Livewire::test('pages::settings.account')->assertForbidden();
});
