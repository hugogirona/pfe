<?php

use App\Models\Announcement;
use App\Models\User;
use App\Notifications\NewAnnouncement;
use App\Notifications\NewFollower;
use Livewire\Livewire;

it('renders an announcement notification and links it to the announcement', function () {
    $user = User::factory()->withProfile()->create();
    $author = User::factory()->withProfile()->create();
    $announcement = Announcement::factory()->for($author)->create(['title' => 'Cherche bassiste']);
    $user->notify(new NewAnnouncement($announcement));

    $id = $user->notifications()->first()->id;

    Livewire::actingAs($user)
        ->test('parts.notifications.panel')
        ->assertSee('Cherche bassiste')
        ->call('open', $id)
        ->assertRedirect(route('announcement', ['id' => $announcement->id]));
});

it('lists the new-follower notifications', function () {
    $user = User::factory()->withProfile()->create();
    $follower = User::factory()->withProfile()->create();
    $user->notify(new NewFollower($follower));

    Livewire::actingAs($user)
        ->test('parts.notifications.panel')
        ->assertSee($follower->full_name);
});

it('renders the follower current avatar, not the one frozen at notification time', function () {
    $user = User::factory()->withProfile()->create();
    $follower = User::factory()->withProfile()->create();
    $follower->profile->update(['avatar_path' => 'old-hash']);

    $user->notify(new NewFollower($follower));

    $follower->profile->update(['avatar_path' => 'new-hash']);

    Livewire::actingAs($user)
        ->test('parts.notifications.panel')
        ->assertSee('new-hash')
        ->assertDontSee('old-hash');
});

it('loads the first 20 notifications and reveals more on demand', function () {
    $user = User::factory()->withProfile()->create();
    $follower = User::factory()->withProfile()->create();

    foreach (range(1, 25) as $i) {
        $user->notify(new NewFollower($follower));
    }

    $component = Livewire::actingAs($user)
        ->test('parts.notifications.panel')
        ->assertSee(__('notifications.load_more'));

    expect($component->instance()->rows())->toHaveCount(20)
        ->and($component->instance()->hasMore())->toBeTrue();

    $component->call('loadMore')
        ->assertDontSee(__('notifications.load_more'));

    expect($component->instance()->rows())->toHaveCount(25)
        ->and($component->instance()->hasMore())->toBeFalse();
});

it('shows an empty state when there is nothing', function () {
    $user = User::factory()->withProfile()->create();

    Livewire::actingAs($user)
        ->test('parts.notifications.panel')
        ->assertSee(__('notifications.empty'));
});

it('marks a notification as read and redirects to the follower profile', function () {
    $user = User::factory()->withProfile()->create();
    $follower = User::factory()->withProfile()->create();
    $user->notify(new NewFollower($follower));
    $id = $user->notifications()->first()->id;

    Livewire::actingAs($user)
        ->test('parts.notifications.panel')
        ->call('open', $id)
        ->assertRedirect(route('profile', ['id' => $follower->profile->id]));

    expect($user->fresh()->unreadNotifications()->count())->toBe(0);
});

it('marks all notifications as read and notifies the badge', function () {
    $user = User::factory()->withProfile()->create();
    $follower = User::factory()->withProfile()->create();
    $user->notify(new NewFollower($follower));

    Livewire::actingAs($user)
        ->test('parts.notifications.panel')
        ->call('markAllRead')
        ->assertDispatched('notifications-updated');

    expect($user->fresh()->unreadNotifications()->count())->toBe(0);
});
