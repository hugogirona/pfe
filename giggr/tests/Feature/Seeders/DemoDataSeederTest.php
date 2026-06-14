<?php

use App\Enums\MediaType;
use App\Models\Announcement;
use App\Models\Follow;
use App\Models\Media;
use App\Models\Profile;
use App\Models\User;
use App\Support\JuryRoster;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    Storage::fake('public');
});

it('does not seed anything in production', function () {
    App::shouldReceive('environment')
        ->with(['local', 'staging'])
        ->andReturn(false);

    $this->seed(DemoDataSeeder::class);

    expect(User::count())->toBe(0);
});

it('seeds 80 demo users plus the developer and the jury accounts in local environment', function () {
    App::partialMock()
        ->shouldReceive('environment')
        ->with(['local', 'staging'])
        ->andReturn(true);

    $this->seed(DemoDataSeeder::class);

    expect(User::count())->toBe(1 + JuryRoster::COUNT + 80);
});

it('seeds the developer and every jury account with stable credentials', function () {
    App::partialMock()
        ->shouldReceive('environment')
        ->with(['local', 'staging'])
        ->andReturn(true);

    $this->seed(DemoDataSeeder::class);

    expect(User::where('email', 'hugo@giggr.be')->exists())->toBeTrue();

    JuryRoster::members()->each(function (array $member) {
        $user = User::where('email', $member['email'])->first();

        expect($user)->not->toBeNull()
            ->and(Hash::check($member['password'], $user->password))->toBeTrue()
            ->and($user->email_verified_at)->not->toBeNull();
    });
});

it('seeds empty jury profiles so the onboarding state can be demonstrated', function () {
    App::partialMock()
        ->shouldReceive('environment')
        ->with(['local', 'staging'])
        ->andReturn(true);

    $this->seed(DemoDataSeeder::class);

    JuryRoster::members()->each(function (array $member) {
        $profile = User::where('email', $member['email'])->first()->profile;

        expect($profile)->not->toBeNull()
            ->and($profile->bio)->toBeNull()
            ->and($profile->avatar_path)->toBeNull()
            ->and($profile->instruments)->toHaveCount(0)
            ->and($profile->genres)->toHaveCount(0)
            ->and($profile->media)->toHaveCount(0);
    });
});

it('seeds a profile for every user', function () {
    App::partialMock()
        ->shouldReceive('environment')
        ->with(['local', 'staging'])
        ->andReturn(true);

    $this->seed(DemoDataSeeder::class);

    expect(Profile::count())->toBe(User::count());
});

it('seeds 150 announcements', function () {
    App::partialMock()
        ->shouldReceive('environment')
        ->with(['local', 'staging'])
        ->andReturn(true);

    $this->seed(DemoDataSeeder::class);

    expect(Announcement::count())->toBe(150);
});

it('seeds approximately 250 follows', function () {
    App::partialMock()
        ->shouldReceive('environment')
        ->with(['local', 'staging'])
        ->andReturn(true);

    $this->seed(DemoDataSeeder::class);

    expect(Follow::count())->toBeGreaterThanOrEqual(250)
        ->and(Follow::count())->toBeLessThanOrEqual(310);
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

it('seeds one image and one video for every non-jury profile', function () {
    App::partialMock()
        ->shouldReceive('environment')
        ->with(['local', 'staging'])
        ->andReturn(true);

    $this->seed(DemoDataSeeder::class);

    $populatedProfiles = Profile::count() - JuryRoster::COUNT;

    expect(Media::where('type', MediaType::Image)->count())->toBe($populatedProfiles)
        ->and(Media::where('type', MediaType::Youtube)->count())->toBe($populatedProfiles);
});

it('generates image variants on disk for the seeded photo', function () {
    App::partialMock()
        ->shouldReceive('environment')
        ->with(['local', 'staging'])
        ->andReturn(true);

    $this->seed(DemoDataSeeder::class);

    $imageMedia = Media::where('type', MediaType::Image)->first();

    Storage::disk('public')
        ->assertExists("media/thumbnail/{$imageMedia->source}.webp")
        ->assertExists("media/medium/{$imageMedia->source}.webp");
});

it('does not seed any media in production', function () {
    App::shouldReceive('environment')
        ->with(['local', 'staging'])
        ->andReturn(false);

    $this->seed(DemoDataSeeder::class);

    expect(Media::count())->toBe(0);
});
