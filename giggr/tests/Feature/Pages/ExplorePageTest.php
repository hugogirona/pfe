<?php

use App\Enums\AnnouncementStatus;
use App\Enums\AnnouncementType;
use App\Models\Announcement;
use App\Models\City;
use App\Models\Profile;
use App\Models\User;
use Database\Seeders\CitySeeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\InstrumentSeeder;
use Livewire\Livewire;

it('explore page loads', function () {
    $this->get(route('explore'))->assertOk();
});

it('explore shows a profile from the database', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $profile = Profile::factory()->create();

    $this->get(route('explore'))
        ->assertOk()
        ->assertSee($profile->user->full_name);
});

it('explore shows an announcement from the database', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $announcement = Announcement::factory()->create();

    $this->get(route('explore'))
        ->assertOk()
        ->assertSee($announcement->title);
});

it('explore hides closed announcements', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $announcement = Announcement::factory()->create(['status' => AnnouncementStatus::Closed]);

    $this->get(route('explore'))
        ->assertOk()
        ->assertDontSee($announcement->title);
});

it('explore hides expired announcements', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $announcement = Announcement::factory()->create([
        'status' => AnnouncementStatus::Open,
        'expires_at' => now()->subDay(),
    ]);

    $this->get(route('explore'))
        ->assertOk()
        ->assertDontSee($announcement->title);
});

it('explore paginates musicians and hides items beyond page one', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    Profile::factory()->count(12)->create();
    $thirteenth = Profile::factory()->create();

    $this->get(route('explore'))
        ->assertOk()
        ->assertDontSee($thirteenth->user->full_name);
});

it('explore paginates announcements and hides items beyond page one', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    Announcement::factory()->count(12)->create();
    $thirteenth = Announcement::factory()->create();

    $this->get(route('explore'))
        ->assertOk()
        ->assertDontSee($thirteenth->title);
});

it('following filter restricts musicians to profiles the viewer follows', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $viewer = User::factory()->withProfile()->create();
    $followed = Profile::factory()->create();
    $other = Profile::factory()->create();
    $viewer->follow($followed);

    $component = Livewire::actingAs($viewer)
        ->test('pages::explore.index')
        ->set('filterFollowing', true);

    $ids = $component->get('filteredMusicians')->pluck('id')->all();
    expect($ids)->toContain($followed->id)
        ->and($ids)->not->toContain($other->id);
});

it('following filter restricts announcements to those of profiles the viewer follows', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $viewer = User::factory()->withProfile()->create();
    $followedAuthor = User::factory()->withProfile()->create();
    $otherAuthor = User::factory()->withProfile()->create();
    $viewer->follow($followedAuthor->profile);

    $followedAnnouncement = Announcement::factory()->for($followedAuthor)->create();
    $otherAnnouncement = Announcement::factory()->for($otherAuthor)->create();

    $component = Livewire::actingAs($viewer)
        ->test('pages::explore.index')
        ->set('filterFollowing', true);

    $titles = $component->get('filteredAnnouncements')->pluck('title')->all();
    expect($titles)->toContain($followedAnnouncement->title)
        ->and($titles)->not->toContain($otherAnnouncement->title);
});

it('following filter is ignored for guests', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    Profile::factory()->count(3)->create();

    $component = Livewire::test('pages::explore.index')
        ->set('filterFollowing', true);

    expect($component->get('filteredMusicians')->total())->toBe(3);
});

it('following filter counts toward activeFiltersCount when set', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $viewer = User::factory()->withProfile()->create();

    Livewire::actingAs($viewer)
        ->test('pages::explore.index')
        ->assertSet('activeFiltersCount', 0)
        ->set('filterFollowing', true)
        ->assertSet('activeFiltersCount', 1);
});

it('filter drawer exposes the following toggle to authenticated users', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $viewer = User::factory()->withProfile()->create();

    $this->actingAs($viewer)
        ->get(route('explore'))
        ->assertOk()
        ->assertSee(__('explore.filter_following'));
});

it('filter drawer hides the following toggle from guests', function () {
    $this->get(route('explore'))
        ->assertOk()
        ->assertDontSee(__('explore.filter_following'));
});

it('radius filter widens musicians to nearby cities of the selected one', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $origin = City::factory()->create(['latitude' => 50.0, 'longitude' => 5.0]);
    $close = City::factory()->create(['latitude' => 50.05, 'longitude' => 5.05]); // ~6 km
    $far = City::factory()->create(['latitude' => 51.0, 'longitude' => 6.0]); // ~131 km

    $hereProfile = Profile::factory()->create(['city_id' => $origin->id]);
    $nearProfile = Profile::factory()->create(['city_id' => $close->id]);
    $farProfile = Profile::factory()->create(['city_id' => $far->id]);

    $component = Livewire::test('pages::explore.index')
        ->set('filterCityId', $origin->id)
        ->set('filterRadius', 50);

    $ids = $component->get('filteredMusicians')->pluck('id')->all();
    expect($ids)->toContain($hereProfile->id)
        ->and($ids)->toContain($nearProfile->id)
        ->and($ids)->not->toContain($farProfile->id);
});

it('radius filter widens announcements to nearby cities of the selected one', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $origin = City::factory()->create(['latitude' => 50.0, 'longitude' => 5.0]);
    $close = City::factory()->create(['latitude' => 50.05, 'longitude' => 5.05]);
    $far = City::factory()->create(['latitude' => 51.0, 'longitude' => 6.0]);

    $hereAnnouncement = Announcement::factory()->create(['city_id' => $origin->id]);
    $nearAnnouncement = Announcement::factory()->create(['city_id' => $close->id]);
    $farAnnouncement = Announcement::factory()->create(['city_id' => $far->id]);

    $component = Livewire::test('pages::explore.index')
        ->set('filterCityId', $origin->id)
        ->set('filterRadius', 50);

    $titles = $component->get('filteredAnnouncements')->pluck('title')->all();
    expect($titles)->toContain($hereAnnouncement->title)
        ->and($titles)->toContain($nearAnnouncement->title)
        ->and($titles)->not->toContain($farAnnouncement->title);
});

it('radius filter falls back to exact city match when radius is 0', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $here = City::factory()->create(['latitude' => 50.0, 'longitude' => 5.0]);
    $near = City::factory()->create(['latitude' => 50.05, 'longitude' => 5.05]);

    $hereProfile = Profile::factory()->create(['city_id' => $here->id]);
    $nearProfile = Profile::factory()->create(['city_id' => $near->id]);

    $component = Livewire::test('pages::explore.index')
        ->set('filterCityId', $here->id)
        ->set('filterRadius', 0);

    $ids = $component->get('filteredMusicians')->pluck('id')->all();
    expect($ids)->toContain($hereProfile->id)
        ->and($ids)->not->toContain($nearProfile->id);
});

it('radius filter is ignored when no city is selected', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    Profile::factory()->count(3)->create();

    $component = Livewire::test('pages::explore.index')
        ->set('filterCityId', null)
        ->set('filterRadius', 25);

    expect($component->get('filteredMusicians')->total())->toBe(3);
});

it('radius counts toward activeFiltersCount only when both city and radius are set', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $city = City::factory()->create();

    Livewire::test('pages::explore.index')
        ->set('filterCityId', null)
        ->set('filterRadius', 50)
        ->assertSet('activeFiltersCount', 0)
        ->set('filterCityId', $city->id)
        ->assertSet('activeFiltersCount', 2);
});

it('filter drawer exposes the radius slider', function () {
    $this->get(route('explore'))
        ->assertOk()
        ->assertSee(__('explore.filter_radius'));
});

it('type filter narrows announcements to selected types only', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    $gig = Announcement::factory()->create(['type' => AnnouncementType::Gig]);
    $lessons = Announcement::factory()->create(['type' => AnnouncementType::Lessons]);
    $musician = Announcement::factory()->create(['type' => AnnouncementType::MusicianWanted]);

    $component = Livewire::test('pages::explore.index')
        ->set('filterTypes', ['gig', 'lessons']);

    $titles = $component->get('filteredAnnouncements')->pluck('title')->all();
    expect($titles)->toContain($gig->title)
        ->and($titles)->toContain($lessons->title)
        ->and($titles)->not->toContain($musician->title);
});

it('type filter is ignored when empty', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);
    Announcement::factory()->count(3)->create();

    $component = Livewire::test('pages::explore.index')
        ->set('filterTypes', []);

    expect($component->get('filteredAnnouncements')->total())->toBe(3);
});

it('type filter counts toward activeFiltersCount per selected type', function () {
    $this->seed([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);

    Livewire::test('pages::explore.index')
        ->assertSet('activeFiltersCount', 0)
        ->set('filterTypes', ['gig', 'lessons'])
        ->assertSet('activeFiltersCount', 2);
});
