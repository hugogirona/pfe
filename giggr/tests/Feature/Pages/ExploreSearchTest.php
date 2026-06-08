<?php

use App\Models\Announcement;
use App\Models\Profile;
use App\Models\User;
use Database\Seeders\CitySeeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\InstrumentSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
});

it('filters the profiles list through the search field', function () {
    $match = User::factory()->withProfile()->create(['first_name' => 'Wolfgang', 'last_name' => 'Amadeus']);
    $other = User::factory()->withProfile()->create(['first_name' => 'Johnny', 'last_name' => 'Cash']);

    $ids = Livewire::test('pages::explore.index')
        ->set('search', 'Amadeus')
        ->get('filteredProfiles')->pluck('id')->all();

    expect($ids)->toContain($match->profile->id)
        ->and($ids)->not->toContain($other->profile->id);
});

it('filters the announcements list through the search field', function () {
    $match = Announcement::factory()->create(['title' => 'Cherche bassiste pour tournée']);
    $other = Announcement::factory()->create(['title' => 'Recherche batteur disponible']);

    $titles = Livewire::test('pages::explore.index')
        ->set('search', 'bassiste')
        ->get('filteredAnnouncements')->pluck('title')->all();

    expect($titles)->toContain($match->title)
        ->and($titles)->not->toContain($other->title);
});

it('resets pagination when the search changes', function () {
    Profile::factory()->count(15)->create();

    Livewire::test('pages::explore.index')
        ->call('setPage', 2, 'profiles-page')
        ->set('search', 'zzz-no-match-resets-page')
        ->assertSet('paginators.profiles-page', 1);
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
