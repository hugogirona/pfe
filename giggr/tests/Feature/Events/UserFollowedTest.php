<?php

use App\Events\UserFollowed;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

it('implements ShouldBroadcastNow for synchronous broadcasting', function () {
    expect(new UserFollowed(42))->toBeInstanceOf(ShouldBroadcastNow::class);
});

it('broadcasts on the notified user private channel', function () {
    $channels = (new UserFollowed(42))->broadcastOn();

    expect($channels)->toHaveCount(1)
        ->and($channels[0])->toBeInstanceOf(PrivateChannel::class)
        ->and($channels[0]->name)->toBe('private-App.Models.User.42');
});

it('uses a short broadcast event name', function () {
    expect((new UserFollowed(42))->broadcastAs())->toBe('notification.created');
});
