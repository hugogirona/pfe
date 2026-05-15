<?php

namespace Tests\Feature\Models;

use App\Models\Announcement;
use App\Models\City;
use App\Models\Profile;
use Illuminate\Database\QueryException;

it('can create a city with all fields', function () {
    $city = City::create([
        'name' => 'Liège',
        'name_alt' => 'Luik',
        'slug' => 'liege-4000',
        'country' => 'BE',
        'postal_code' => '4000',
        'latitude' => 50.6326,
        'longitude' => 5.5797,
        'searchable' => 'liege luik 4000',
    ]);

    expect($city->fresh())
        ->name->toBe('Liège')
        ->name_alt->toBe('Luik')
        ->slug->toBe('liege-4000')
        ->country->toBe('BE')
        ->postal_code->toBe('4000')
        ->latitude->toEqual(50.6326)
        ->longitude->toEqual(5.5797)
        ->searchable->toBe('liege luik 4000');
});

it('defaults country to BE when not provided', function () {
    $city = City::create([
        'name' => 'Bruxelles',
        'slug' => 'bruxelles-1000',
        'postal_code' => '1000',
        'searchable' => 'bruxelles brussel 1000',
        'latitude' => 50.8466,
        'longitude' => 4.3528,
    ]);

    expect($city->fresh()->country)->toBe('BE');
});

it('allows null name_alt but requires latitude and longitude', function () {
    $city = City::create([
        'name' => 'Maldegem',
        'slug' => 'maldegem-9990',
        'postal_code' => '9990',
        'searchable' => 'maldegem 9990',
        'latitude' => 51.213,
        'longitude' => 3.450,
    ]);

    expect($city->fresh()->name_alt)->toBeNull()
        ->and($city->fresh()->latitude)->toEqual(51.213)
        ->and($city->fresh()->longitude)->toEqual(3.450);
});

it('enforces a unique slug', function () {
    City::create([
        'name' => 'Liège', 'slug' => 'liege-4000', 'postal_code' => '4000', 'searchable' => 'liege 4000',
        'latitude' => 50.6326, 'longitude' => 5.5797,
    ]);

    expect(fn () => City::create([
        'name' => 'Liège', 'slug' => 'liege-4000', 'postal_code' => '4000', 'searchable' => 'liege 4000',
        'latitude' => 50.6326, 'longitude' => 5.5797,
    ]))->toThrow(QueryException::class);
});

it('exposes a working factory', function () {
    $city = City::factory()->create();

    expect($city)->toBeInstanceOf(City::class)
        ->and($city->slug)->not->toBeEmpty()
        ->and($city->postal_code)->not->toBeEmpty()
        ->and($city->searchable)->not->toBeEmpty();
});

it('makeSearchable normalizes name + alt + postal into a slug joined by single spaces', function () {
    expect(City::makeSearchable('Liège', 'Luik', '4000'))->toBe('liege luik 4000')
        ->and(City::makeSearchable('Maldegem', null, '9990'))->toBe('maldegem 9990')
        ->and(City::makeSearchable('Maldegem', '', '9990'))->toBe('maldegem 9990');
});

it('exposes display_name accessor as "name (postal_code)"', function () {
    $city = City::factory()->create([
        'name' => 'Liège', 'postal_code' => '4000',
    ]);

    expect($city->display_name)->toBe('Liège (4000)');
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

it('nearby scope returns cities within the radius and excludes the rest', function () {
    $origin = City::factory()->create(['latitude' => 50.0, 'longitude' => 5.0]);
    $close = City::factory()->create(['latitude' => 50.05, 'longitude' => 5.05]); // ~6 km
    $mid = City::factory()->create(['latitude' => 50.5, 'longitude' => 5.5]); // ~62 km
    $far = City::factory()->create(['latitude' => 51.0, 'longitude' => 6.0]); // ~125 km

    $ids = City::query()
        ->nearby(50.0, 5.0, 100)
        ->whereIn('id', [$origin->id, $close->id, $mid->id, $far->id])
        ->pluck('id')
        ->all();

    expect($ids)->toContain($origin->id)
        ->and($ids)->toContain($close->id)
        ->and($ids)->toContain($mid->id)
        ->and($ids)->not->toContain($far->id);
});

it('nearby scope tightens results when the radius shrinks', function () {
    $origin = City::factory()->create(['latitude' => 50.0, 'longitude' => 5.0]);
    $close = City::factory()->create(['latitude' => 50.05, 'longitude' => 5.05]);
    $mid = City::factory()->create(['latitude' => 50.5, 'longitude' => 5.5]);

    $ids = City::query()->nearby(50.0, 5.0, 20)->whereIn('id', [$origin->id, $close->id, $mid->id])->pluck('id')->all();

    expect($ids)->toContain($origin->id)
        ->and($ids)->toContain($close->id)
        ->and($ids)->not->toContain($mid->id);
});

it('nearby scope returns the origin city only when radius is 0', function () {
    $origin = City::factory()->create(['latitude' => 50.0, 'longitude' => 5.0]);
    $close = City::factory()->create(['latitude' => 50.05, 'longitude' => 5.05]);

    $ids = City::query()->nearby(50.0, 5.0, 0)->whereIn('id', [$origin->id, $close->id])->pluck('id')->all();

    expect($ids)->toContain($origin->id)
        ->and($ids)->not->toContain($close->id);
});
