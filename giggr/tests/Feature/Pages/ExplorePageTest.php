<?php

use App\Enums\AnnouncementStatus;
use App\Models\Announcement;
use App\Models\Profile;
use Database\Seeders\CitySeeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\InstrumentSeeder;

it('explore page loads', function () {
    $this->get(route('explore'))->assertOk();
});

it('explore shows a profile from the database', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();

    $this->get(route('explore'))
        ->assertOk()
        ->assertSee($profile->user->full_name);
});

it('explore shows an announcement from the database', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $announcement = Announcement::factory()->create();

    $this->get(route('explore'))
        ->assertOk()
        ->assertSee($announcement->title);
});

it('explore hides closed announcements', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $announcement = Announcement::factory()->create(['status' => AnnouncementStatus::Closed]);

    $this->get(route('explore'))
        ->assertOk()
        ->assertDontSee($announcement->title);
});

it('explore hides expired announcements', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $announcement = Announcement::factory()->create([
        'status' => AnnouncementStatus::Open,
        'expires_at' => now()->subDay(),
    ]);

    $this->get(route('explore'))
        ->assertOk()
        ->assertDontSee($announcement->title);
});
