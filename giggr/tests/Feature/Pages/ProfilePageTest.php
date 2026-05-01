<?php

use App\Models\Profile;
use App\Models\User;
use Database\Seeders\CitySeeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\InstrumentSeeder;

it('profile page renders a real profile', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();

    $this->actingAs($profile->user)
        ->get(route('profile', ['id' => $profile->id]))
        ->assertOk()
        ->assertSee($profile->user->full_name);
});

it('profile page returns 404 for a missing profile', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('profile', ['id' => 99999]))
        ->assertNotFound();
});

it('profile page redirects guests to login', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();

    $this->get(route('profile', ['id' => $profile->id]))
        ->assertRedirectToRoute('login');
});
