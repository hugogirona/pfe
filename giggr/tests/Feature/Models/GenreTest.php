<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use App\Models\Profile;
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
    $genre    = Genre::factory()->create();
    $profiles = Profile::factory()->count(2)->create();

    $profiles->each(fn ($p) => $p->genres()->attach($genre));

    expect($genre->profiles)->toHaveCount(2);
});
