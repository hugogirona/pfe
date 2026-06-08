<?php

use App\Models\Announcement;
use App\Models\Genre;
use App\Models\Instrument;
use App\Models\Profile;
use App\Models\User;
use Database\Seeders\CitySeeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\InstrumentSeeder;

beforeEach(function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
});

it('matches a profile by the user first or last name', function () {
    $match = User::factory()->withProfile()->create(['first_name' => 'Wolfgang', 'last_name' => 'Amadeus']);
    $other = User::factory()->withProfile()->create(['first_name' => 'Johnny', 'last_name' => 'Cash']);

    $ids = Profile::query()->search('Amadeus')->pluck('id')->all();

    expect($ids)->toContain($match->profile->id)
        ->and($ids)->not->toContain($other->profile->id);
});

it('matches a profile by an instrument name', function () {
    $instrument = Instrument::factory()->create(['name' => 'Theremin']);
    $match = Profile::factory()->create();
    $match->instruments()->sync([$instrument->id]);
    $other = tap(Profile::factory()->create(), function (Profile $p) {
        $p->instruments()->sync([]);
        $p->genres()->sync([]);
    });

    $ids = Profile::query()->search('Theremin')->pluck('id')->all();

    expect($ids)->toContain($match->id)
        ->and($ids)->not->toContain($other->id);
});

it('matches a profile by a genre name', function () {
    $genre = Genre::factory()->create(['name' => 'Vaporwave']);
    $match = Profile::factory()->create();
    $match->genres()->sync([$genre->id]);
    $other = tap(Profile::factory()->create(), function (Profile $p) {
        $p->instruments()->sync([]);
        $p->genres()->sync([]);
    });

    $ids = Profile::query()->search('Vaporwave')->pluck('id')->all();

    expect($ids)->toContain($match->id)
        ->and($ids)->not->toContain($other->id);
});

it('matches an announcement by its title', function () {
    $match = Announcement::factory()->create(['title' => 'Cherche bassiste pour tournée']);
    $other = Announcement::factory()->create(['title' => 'Recherche batteur disponible']);

    $ids = Announcement::query()->search('bassiste')->pluck('id')->all();

    expect($ids)->toContain($match->id)
        ->and($ids)->not->toContain($other->id);
});

it('matches an announcement by an instrument name', function () {
    $instrument = Instrument::factory()->create(['name' => 'Theremin']);
    $match = Announcement::factory()->create();
    $match->instruments()->sync([$instrument->id]);
    $other = tap(Announcement::factory()->create(), function (Announcement $a) {
        $a->instruments()->sync([]);
        $a->genres()->sync([]);
    });

    $ids = Announcement::query()->search('Theremin')->pluck('id')->all();

    expect($ids)->toContain($match->id)
        ->and($ids)->not->toContain($other->id);
});

it('matches an announcement by a genre name', function () {
    $genre = Genre::factory()->create(['name' => 'Vaporwave']);
    $match = Announcement::factory()->create();
    $match->genres()->sync([$genre->id]);
    $other = tap(Announcement::factory()->create(), function (Announcement $a) {
        $a->instruments()->sync([]);
        $a->genres()->sync([]);
    });

    $ids = Announcement::query()->search('Vaporwave')->pluck('id')->all();

    expect($ids)->toContain($match->id)
        ->and($ids)->not->toContain($other->id);
});

it('requires every word of a multi-word query to match (AND)', function () {
    $match = User::factory()->withProfile()->create(['first_name' => 'Wolfgang', 'last_name' => 'Amadeus']);
    $other = User::factory()->withProfile()->create(['first_name' => 'Johnny', 'last_name' => 'Cash']);

    $hit = Profile::query()->search('Wolfgang Amadeus')->pluck('id')->all();
    $miss = Profile::query()->search('Wolfgang Cash')->pluck('id')->all();

    expect($hit)->toContain($match->profile->id)
        ->and($miss)->not->toContain($match->profile->id)
        ->and($miss)->not->toContain($other->profile->id);
});

it('applies no constraint for an empty or whitespace-only query', function () {
    Profile::factory()->count(3)->create();

    expect(Profile::query()->search('')->count())->toBe(3)
        ->and(Profile::query()->search('   ')->count())->toBe(3);
});
