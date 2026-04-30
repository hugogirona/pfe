<?php

namespace Tests\Feature\Models;

use App\Models\Instrument;
use Illuminate\Database\QueryException;

it('can create an instrument with name and slug', function () {
    $instrument = Instrument::create(['name' => 'Guitare', 'slug' => 'guitare']);

    expect($instrument->fresh())
        ->name->toBe('Guitare')
        ->slug->toBe('guitare');
});

it('enforces a unique slug', function () {
    Instrument::create(['name' => 'Guitare', 'slug' => 'guitare']);

    expect(fn () => Instrument::create(['name' => 'Guitare 2', 'slug' => 'guitare']))
        ->toThrow(QueryException::class);
});

it('exposes a working factory', function () {
    $instrument = Instrument::factory()->create();

    expect($instrument)->toBeInstanceOf(Instrument::class)
        ->and($instrument->slug)->not->toBeEmpty();
});
