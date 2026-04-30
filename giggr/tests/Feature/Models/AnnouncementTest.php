<?php

use App\Enums\AnnouncementStatus;
use App\Enums\AnnouncementType;
use App\Models\Announcement;
use App\Models\City;
use App\Models\Genre;
use App\Models\Instrument;
use App\Models\User;
use Database\Seeders\CitySeeder;
use Illuminate\Support\Carbon;

it('can be created with required fields', function () {
    $user = User::factory()->create();
    $city = City::factory()->create();

    $announcement = Announcement::create([
        'user_id' => $user->id,
        'city_id' => $city->id,
        'title' => 'Recherche guitariste',
        'description' => 'On cherche un guitariste pour notre groupe.',
        'type' => AnnouncementType::Search,
    ]);

    expect($announcement->fresh())
        ->toBeInstanceOf(Announcement::class)
        ->title->toBe('Recherche guitariste')
        ->type->toBe(AnnouncementType::Search)
        ->status->toBe(AnnouncementStatus::Open);
});

it('has a default status of open', function () {
    $announcement = Announcement::factory()->create();

    expect($announcement->fresh()->status)->toBe(AnnouncementStatus::Open);
});

it('casts type to AnnouncementType enum', function () {
    $announcement = Announcement::factory()->create(['type' => AnnouncementType::Course]);

    expect($announcement->fresh()->type)->toBe(AnnouncementType::Course);
});

it('casts status to AnnouncementStatus enum', function () {
    $announcement = Announcement::factory()->create(['status' => AnnouncementStatus::Closed]);

    expect($announcement->fresh()->status)->toBe(AnnouncementStatus::Closed);
});

it('casts expires_at to datetime', function () {
    $date = now()->addDays(14);
    $announcement = Announcement::factory()->create(['expires_at' => $date]);

    expect($announcement->fresh()->expires_at)->toBeInstanceOf(Carbon::class);
});

it('allows null expires_at', function () {
    $announcement = Announcement::factory()->create(['expires_at' => null]);

    expect($announcement->fresh()->expires_at)->toBeNull();
});

it('belongs to a user', function () {
    $user = User::factory()->create();
    $announcement = Announcement::factory()->create(['user_id' => $user->id]);

    expect($announcement->user->id)->toBe($user->id);
});

it('belongs to a city', function () {
    $city = City::factory()->create();
    $announcement = Announcement::factory()->create(['city_id' => $city->id]);

    expect($announcement->city->id)->toBe($city->id);
});

it('syncs instruments via pivot', function () {
    $announcement = Announcement::factory()->create();
    $instruments = Instrument::factory()->count(2)->create();

    $announcement->instruments()->sync($instruments->pluck('id'));

    expect($announcement->instruments)->toHaveCount(2);
});

it('syncs genres via pivot', function () {
    $announcement = Announcement::factory()->create();
    $genres = Genre::factory()->count(2)->create();

    $announcement->genres()->sync($genres->pluck('id'));

    expect($announcement->genres)->toHaveCount(2);
});

it('open scope returns only open announcements', function () {
    Announcement::factory()->create(['status' => AnnouncementStatus::Open]);
    Announcement::factory()->create(['status' => AnnouncementStatus::Closed]);
    Announcement::factory()->create(['status' => AnnouncementStatus::Expired]);

    expect(Announcement::open()->count())->toBe(1);
});

it('active scope returns open announcements that have not expired', function () {
    Announcement::factory()->create(['status' => AnnouncementStatus::Open, 'expires_at' => null]);
    Announcement::factory()->create(['status' => AnnouncementStatus::Open, 'expires_at' => now()->addDays(7)]);
    Announcement::factory()->create(['status' => AnnouncementStatus::Open, 'expires_at' => now()->subDay()]);
    Announcement::factory()->create(['status' => AnnouncementStatus::Closed, 'expires_at' => null]);

    expect(Announcement::active()->count())->toBe(2);
});

it('is excluded from default query when soft-deleted', function () {
    $announcement = Announcement::factory()->create();

    $announcement->delete();

    expect(Announcement::find($announcement->id))->toBeNull()
        ->and(Announcement::withTrashed()->find($announcement->id))->not->toBeNull();
});

it('exposes a working factory', function () {
    $announcement = Announcement::factory()->create();

    expect($announcement)->toBeInstanceOf(Announcement::class)
        ->and($announcement->title)->not->toBeEmpty();
});

it('factory picks a seeded city when cities exist', function () {
    $this->seed(CitySeeder::class);

    $cityIds = City::pluck('id');
    $announcement = Announcement::factory()->create();

    expect($cityIds->toArray())->toContain($announcement->city_id);
});
