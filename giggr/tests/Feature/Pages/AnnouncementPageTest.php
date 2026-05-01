<?php

use App\Models\Announcement;
use App\Models\Profile;
use App\Models\User;
use Database\Seeders\CitySeeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\InstrumentSeeder;

it('announcement page renders a real announcement', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();
    $announcement = Announcement::factory()->create(['user_id' => $profile->user_id]);

    $this->actingAs($profile->user)
        ->get(route('announcement', ['id' => $announcement->id]))
        ->assertOk()
        ->assertSee($announcement->title);
});

it('announcement page returns 404 for a missing announcement', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('announcement', ['id' => 99999]))
        ->assertNotFound();
});

it('announcement page redirects guests to login', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();
    $announcement = Announcement::factory()->create(['user_id' => $profile->user_id]);

    $this->get(route('announcement', ['id' => $announcement->id]))
        ->assertRedirectToRoute('login');
});
