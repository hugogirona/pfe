<?php

namespace Tests\Feature\Models;

use App\Models\Announcement;
use App\Models\City;
use App\Models\Genre;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\QueryException;

it('can create a genre with name and slug', function () {
    $genre = Genre::create(['name' => 'Rock', 'slug' => 'rock']);

    expect($genre->fresh())
        ->name->toBe('Rock')
        ->slug->toBe('rock');
});

it('enforces a unique slug', function () {
    Genre::create(['name' => 'Rock', 'slug' => 'rock']);

    expect(fn () => Genre::create(['name' => 'Rock 2', 'slug' => 'rock']))
        ->toThrow(QueryException::class);
});

it('exposes a working factory', function () {
    $genre = Genre::factory()->create();

    expect($genre)->toBeInstanceOf(Genre::class)
        ->and($genre->slug)->not->toBeEmpty();
});

it('belongs to many profiles', function () {
    $genre = Genre::factory()->create();
    $profiles = collect([
        Profile::create(['user_id' => User::factory()->create()->id]),
        Profile::create(['user_id' => User::factory()->create()->id]),
    ]);

    $profiles->each(fn ($p) => $p->genres()->attach($genre));

    expect($genre->profiles)->toHaveCount(2);
});

it('belongs to many announcements', function () {
    $genre         = Genre::factory()->create();
    $announcements = collect([
        Announcement::create(['user_id' => User::factory()->create()->id, 'city_id' => City::factory()->create()->id, 'title' => 'A', 'description' => 'B', 'type' => 'search']),
        Announcement::create(['user_id' => User::factory()->create()->id, 'city_id' => City::factory()->create()->id, 'title' => 'C', 'description' => 'D', 'type' => 'session']),
    ]);

    $announcements->each(fn ($a) => $a->genres()->attach($genre));

    expect($genre->announcements)->toHaveCount(2);
});
