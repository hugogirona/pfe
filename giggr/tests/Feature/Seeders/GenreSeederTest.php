<?php

namespace Tests\Feature\Seeders;

use App\Models\Genre;
use Database\Seeders\GenreSeeder;

it('seeds 12 genres', function () {
    $this->seed(GenreSeeder::class);

    expect(Genre::count())->toBe(12);
});

it('is idempotent', function () {
    $this->seed(GenreSeeder::class);
    $this->seed(GenreSeeder::class);

    expect(Genre::count())->toBe(12);
});

it('seeds Rock', function () {
    $this->seed(GenreSeeder::class);

    expect(Genre::where('slug', 'rock')->exists())->toBeTrue();
});
