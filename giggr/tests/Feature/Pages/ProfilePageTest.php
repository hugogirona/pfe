<?php

use App\Models\Profile;
use Database\Seeders\CitySeeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\InstrumentSeeder;

it('profile page renders a real profile', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();

    $this->get(route('profile', ['id' => $profile->id]))
        ->assertOk()
        ->assertSee($profile->user->full_name);
});

it('profile page returns 404 for a missing profile', function () {
    $this->get(route('profile', ['id' => 99999]))
        ->assertNotFound();
});
