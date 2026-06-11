<?php

use App\Events\NotificationCreated;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

it('implements ShouldBroadcastNow for synchronous broadcasting', function () {
    expect(new NotificationCreated(42))->toBeInstanceOf(ShouldBroadcastNow::class);
});

it('broadcasts on the notified user private channel', function () {
    $channels = (new NotificationCreated(42))->broadcastOn();

    expect($channels)->toHaveCount(1)
        ->and($channels[0])->toBeInstanceOf(PrivateChannel::class)
        ->and($channels[0]->name)->toBe('private-App.Models.User.42');
});

it('uses a short broadcast event name', function () {
    expect((new NotificationCreated(42))->broadcastAs())->toBe('notification.created');
});
