<?php

use App\Models\Announcement;
use App\Models\Follow;
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

it('seeds 30 demo users plus the developer account in local environment', function () {
    App::partialMock()
        ->shouldReceive('environment')
        ->with(['local', 'staging'])
        ->andReturn(true);

    $this->seed(DemoDataSeeder::class);

    expect(User::count())->toBe(31);
});

it('seeds the developer account with a stable email', function () {
    App::partialMock()
        ->shouldReceive('environment')
        ->with(['local', 'staging'])
        ->andReturn(true);

    $this->seed(DemoDataSeeder::class);

    expect(User::where('email', 'hello@giggr.com')->exists())->toBeTrue();
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

it('seeds approximately 100 follows', function () {
    App::partialMock()
        ->shouldReceive('environment')
        ->with(['local', 'staging'])
        ->andReturn(true);

    $this->seed(DemoDataSeeder::class);

    expect(Follow::count())->toBeGreaterThanOrEqual(80)
        ->and(Follow::count())->toBeLessThanOrEqual(120);
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

it('every follow belongs to a seeded user', function () {
    App::partialMock()
        ->shouldReceive('environment')
        ->with(['local', 'staging'])
        ->andReturn(true);

    $this->seed(DemoDataSeeder::class);

    $userIds = User::pluck('id');

    expect(Follow::whereNotIn('user_id', $userIds)->count())->toBe(0);
});
