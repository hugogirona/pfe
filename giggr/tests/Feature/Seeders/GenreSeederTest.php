<?php

namespace Tests\Feature\Seeders;

use App\Models\Genre;
use Database\Seeders\GenreSeeder;

it('seeds the genre catalogue', function () {
    $this->seed(GenreSeeder::class);

    expect(Genre::count())->toBe(35);
});

it('is idempotent', function () {
    $this->seed(GenreSeeder::class);
    $count = Genre::count();
    $this->seed(GenreSeeder::class);

    expect(Genre::count())->toBe($count);
});

it('seeds Rock', function () {
    $this->seed(GenreSeeder::class);

    expect(Genre::where('slug', 'rock')->exists())->toBeTrue();
});
