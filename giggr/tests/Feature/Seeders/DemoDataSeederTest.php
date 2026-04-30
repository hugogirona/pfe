<?php

use App\Models\Announcement;
use App\Models\Favorite;
use App\Models\Profile;
use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Support\Facades\App;

it('does not seed anything in production', function () {
    App::shouldReceive('environment')
        ->with(['local', 'staging'])
        ->andReturn(false);

    $this->seed(DemoDataSeeder::class);

    expect(User::count())->toBe(0);
});

it('seeds 30 users in local environment', function () {
    App::partialMock()
        ->shouldReceive('environment')
        ->with(['local', 'staging'])
        ->andReturn(true);

    $this->seed(DemoDataSeeder::class);

    expect(User::count())->toBe(30);
});

it('seeds a profile for every user', function () {
    App::partialMock()
        ->shouldReceive('environment')
        ->with(['local', 'staging'])
        ->andReturn(true);

    $this->seed(DemoDataSeeder::class);

    expect(Profile::count())->toBe(User::count());
});

it('seeds approximately 50 announcements', function () {
    App::partialMock()
        ->shouldReceive('environment')
        ->with(['local', 'staging'])
        ->andReturn(true);

    $this->seed(DemoDataSeeder::class);

    expect(Announcement::count())->toBeGreaterThanOrEqual(40)
        ->and(Announcement::count())->toBeLessThanOrEqual(60);
});

it('seeds approximately 100 favorites', function () {
    App::partialMock()
        ->shouldReceive('environment')
        ->with(['local', 'staging'])
        ->andReturn(true);

    $this->seed(DemoDataSeeder::class);

    expect(Favorite::count())->toBeGreaterThanOrEqual(80)
        ->and(Favorite::count())->toBeLessThanOrEqual(120);
});

it('every announcement belongs to a seeded user', function () {
    App::partialMock()
        ->shouldReceive('environment')
        ->with(['local', 'staging'])
        ->andReturn(true);

    $this->seed(DemoDataSeeder::class);

    $userIds = User::pluck('id');

    expect(Announcement::whereNotIn('user_id', $userIds)->count())->toBe(0);
});

it('every favorite belongs to a seeded user', function () {
    App::partialMock()
        ->shouldReceive('environment')
        ->with(['local', 'staging'])
        ->andReturn(true);

    $this->seed(DemoDataSeeder::class);

    $userIds = User::pluck('id');

    expect(Favorite::whereNotIn('user_id', $userIds)->count())->toBe(0);
});
