<?php

use App\Enums\AnnouncementStatus;
use App\Models\Announcement;
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
