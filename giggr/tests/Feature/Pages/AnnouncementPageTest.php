<?php

use App\Models\Announcement;
use App\Models\Profile;
use Database\Seeders\CitySeeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\InstrumentSeeder;

it('announcement page renders a real announcement', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();
    $announcement = Announcement::factory()->create(['user_id' => $profile->user_id]);

    $this->get(route('announcement', ['id' => $announcement->id]))
        ->assertOk()
        ->assertSee($announcement->title);
});

it('announcement page returns 404 for a missing announcement', function () {
    $this->get(route('announcement', ['id' => 99999]))
        ->assertNotFound();
});
