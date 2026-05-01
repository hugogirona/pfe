<?php

use App\Models\Profile;
use Database\Seeders\CitySeeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\InstrumentSeeder;
use Livewire\Livewire;

it('musician slider renders without error', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    Profile::factory()->count(3)->create();

    Livewire::test('parts.home.musician-slider')->assertOk();
});

it('musician slider exposes profiles collection', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    Profile::factory()->count(3)->create();

    Livewire::test('parts.home.musician-slider')
        ->assertSet('profiles', fn ($profiles) => $profiles->count() === 3);
});

it('musician slider caps profiles at 7', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    Profile::factory()->count(10)->create();

    Livewire::test('parts.home.musician-slider')
        ->assertSet('profiles', fn ($profiles) => $profiles->count() === 7);
});
