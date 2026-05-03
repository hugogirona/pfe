<?php

use App\Models\Profile;
use App\Models\User;
use Database\Seeders\CitySeeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\InstrumentSeeder;

it('owner does not see social actions', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();

    $this->actingAs($profile->user)
        ->get(route('profile', ['id' => $profile->id]))
        ->assertDontSee(__('profile.contact_name', ['name' => $profile->user->full_name]));
});

it('visitor sees social actions', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();
    $visitor = User::factory()->create();

    $this->actingAs($visitor)
        ->get(route('profile', ['id' => $profile->id]))
        ->assertSee(__('profile.contact_name', ['name' => $profile->user->full_name]));
});

it('owner without bio sees add bio empty state', function () {
    $user = User::factory()->create();
    $profile = Profile::create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('profile', ['id' => $profile->id]))
        ->assertSee(__('profile.add_bio_empty'));
});

it('visitor does not see add bio empty state when bio is missing', function () {
    $user = User::factory()->create();
    $profile = Profile::create(['user_id' => $user->id]);
    $visitor = User::factory()->create();

    $this->actingAs($visitor)
        ->get(route('profile', ['id' => $profile->id]))
        ->assertDontSee(__('profile.add_bio_empty'));
});

it('owner sees owner gallery empty state', function () {
    $user = User::factory()->create();
    $profile = Profile::create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('profile', ['id' => $profile->id]))
        ->assertSee(__('profile.gallery_empty_owner'))
        ->assertDontSee(__('profile.gallery_empty'));
});

it('visitor sees visitor gallery empty state', function () {
    $user = User::factory()->create();
    $profile = Profile::create(['user_id' => $user->id]);
    $visitor = User::factory()->create();

    $this->actingAs($visitor)
        ->get(route('profile', ['id' => $profile->id]))
        ->assertSee(__('profile.gallery_empty'))
        ->assertDontSee(__('profile.gallery_empty_owner'));
});

it('owner sees owner announcements empty state', function () {
    $user = User::factory()->create();
    $profile = Profile::create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('profile', ['id' => $profile->id]))
        ->assertSee(__('profile.announcements_empty_owner'))
        ->assertDontSee(__('profile.announcements_empty', ['name' => $user->full_name]));
});

it('visitor sees visitor announcements empty state', function () {
    $user = User::factory()->create();
    $profile = Profile::create(['user_id' => $user->id]);
    $visitor = User::factory()->create();

    $this->actingAs($visitor)
        ->get(route('profile', ['id' => $profile->id]))
        ->assertSee(__('profile.announcements_empty', ['name' => $user->full_name]))
        ->assertDontSee(__('profile.announcements_empty_owner'));
});
