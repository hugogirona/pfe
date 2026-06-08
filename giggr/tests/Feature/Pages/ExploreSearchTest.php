<?php

use App\Models\Announcement;
use App\Models\Genre;
use App\Models\Instrument;
use App\Models\Profile;
use App\Models\User;
use Database\Seeders\CitySeeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\InstrumentSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
});

it('searches profiles by user first or last name', function () {
    $match = User::factory()->withProfile()->create(['first_name' => 'Wolfgang', 'last_name' => 'Amadeus']);
    $other = User::factory()->withProfile()->create(['first_name' => 'Johnny', 'last_name' => 'Cash']);

    $ids = Livewire::test('pages::explore.index')
        ->set('search', 'Amadeus')
        ->get('filteredProfiles')->pluck('id')->all();

    expect($ids)->toContain($match->profile->id)
        ->and($ids)->not->toContain($other->profile->id);
});

it('searches profiles by instrument name', function () {
    $instrument = Instrument::factory()->create(['name' => 'Theremin']);
    $match = Profile::factory()->create();
    $match->instruments()->sync([$instrument->id]);
    $other = Profile::factory()->create();
    $other->instruments()->sync([]);
    $other->genres()->sync([]);

    $ids = Livewire::test('pages::explore.index')
        ->set('search', 'Theremin')
        ->get('filteredProfiles')->pluck('id')->all();

    expect($ids)->toContain($match->id)
        ->and($ids)->not->toContain($other->id);
});

it('searches profiles by genre name', function () {
    $genre = Genre::factory()->create(['name' => 'Vaporwave']);
    $match = Profile::factory()->create();
    $match->genres()->sync([$genre->id]);
    $other = Profile::factory()->create();
    $other->instruments()->sync([]);
    $other->genres()->sync([]);

    $ids = Livewire::test('pages::explore.index')
        ->set('search', 'Vaporwave')
        ->get('filteredProfiles')->pluck('id')->all();

    expect($ids)->toContain($match->id)
        ->and($ids)->not->toContain($other->id);
});

it('searches announcements by title', function () {
    $match = Announcement::factory()->create(['title' => 'Cherche bassiste pour tournée']);
    $other = Announcement::factory()->create(['title' => 'Recherche batteur disponible']);

    $titles = Livewire::test('pages::explore.index')
        ->set('search', 'bassiste')
        ->get('filteredAnnouncements')->pluck('title')->all();

    expect($titles)->toContain($match->title)
        ->and($titles)->not->toContain($other->title);
});

it('searches announcements by instrument name', function () {
    $instrument = Instrument::factory()->create(['name' => 'Theremin']);
    $match = Announcement::factory()->create();
    $match->instruments()->sync([$instrument->id]);
    $other = Announcement::factory()->create();
    $other->instruments()->sync([]);
    $other->genres()->sync([]);

    $titles = Livewire::test('pages::explore.index')
        ->set('search', 'Theremin')
        ->get('filteredAnnouncements')->pluck('id')->all();

    expect($titles)->toContain($match->id)
        ->and($titles)->not->toContain($other->id);
});

it('searches announcements by genre name', function () {
    $genre = Genre::factory()->create(['name' => 'Vaporwave']);
    $match = Announcement::factory()->create();
    $match->genres()->sync([$genre->id]);
    $other = Announcement::factory()->create();
    $other->instruments()->sync([]);
    $other->genres()->sync([]);

    $ids = Livewire::test('pages::explore.index')
        ->set('search', 'Vaporwave')
        ->get('filteredAnnouncements')->pluck('id')->all();

    expect($ids)->toContain($match->id)
        ->and($ids)->not->toContain($other->id);
});

it('requires every word of a multi-word search to match', function () {
    $match = User::factory()->withProfile()->create(['first_name' => 'Wolfgang', 'last_name' => 'Amadeus']);
    $other = User::factory()->withProfile()->create(['first_name' => 'Johnny', 'last_name' => 'Cash']);

    $hit = Livewire::test('pages::explore.index')
        ->set('search', 'Wolfgang Amadeus')
        ->get('filteredProfiles')->pluck('id')->all();

    $miss = Livewire::test('pages::explore.index')
        ->set('search', 'Wolfgang Cash')
        ->get('filteredProfiles')->pluck('id')->all();

    expect($hit)->toContain($match->profile->id)
        ->and($miss)->not->toContain($match->profile->id)
        ->and($miss)->not->toContain($other->profile->id);
});

it('ignores an empty or whitespace-only search', function () {
    Profile::factory()->count(3)->create();

    $total = Livewire::test('pages::explore.index')
        ->set('search', '   ')
        ->get('filteredProfiles')->total();

    expect($total)->toBe(3);
});

it('does not count the search toward the active filters badge', function () {
    Livewire::test('pages::explore.index')
        ->set('search', 'mozart')
        ->assertSet('activeFiltersCount', 0);
});

it('hydrates the search from the q query string parameter', function () {
    Livewire::withQueryParams(['q' => 'mozart'])
        ->test('pages::explore.index')
        ->assertSet('search', 'mozart');
});

it('renders the search bar on the explore page', function () {
    $this->get(route('explore'))
        ->assertOk()
        ->assertSee(__('explore.search_placeholder'), false)
        ->assertSee(__('explore.search_label'), false);
});
