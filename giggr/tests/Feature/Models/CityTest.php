<?php

namespace Tests\Feature\Models;

use App\Models\Announcement;
use App\Models\City;
use App\Models\Profile;
use Illuminate\Database\QueryException;

it('can create a city with name, slug, country and coordinates', function () {
    $city = City::create([
        'name' => 'Liège',
        'slug' => 'liege',
        'country' => 'BE',
        'latitude' => 50.6326,
        'longitude' => 5.5797,
    ]);

    expect($city->fresh())
        ->name->toBe('Liège')
        ->slug->toBe('liege')
        ->country->toBe('BE')
        ->latitude->toEqual(50.6326)
        ->longitude->toEqual(5.5797);
});

it('defaults country to BE when not provided', function () {
    $city = City::create(['name' => 'Bruxelles', 'slug' => 'bruxelles']);

    expect($city->fresh()->country)->toBe('BE');
});

it('allows null latitude and longitude', function () {
    $city = City::create(['name' => 'Tournai', 'slug' => 'tournai']);

    expect($city->fresh())
        ->latitude->toBeNull()
        ->longitude->toBeNull();
});

it('enforces a unique slug', function () {
    City::create(['name' => 'Liège', 'slug' => 'liege']);

    expect(fn () => City::create(['name' => 'Liege2', 'slug' => 'liege']))
        ->toThrow(QueryException::class);
});

it('exposes a working factory', function () {
    $city = City::factory()->create();

    expect($city)->toBeInstanceOf(City::class)
        ->and($city->slug)->not->toBeEmpty();
});

it('has many profiles', function () {
    $city = City::factory()->create();
    Profile::factory()->count(2)->create(['city_id' => $city->id]);

    expect($city->profiles)->toHaveCount(2);
});

it('has many announcements', function () {
    $city = City::factory()->create();
    Announcement::factory()->count(3)->create(['city_id' => $city->id]);

    expect($city->announcements)->toHaveCount(3);
});
