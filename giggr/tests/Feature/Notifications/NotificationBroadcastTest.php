<?php

use App\Events\NotificationCreated;
use App\Models\User;
use App\Notifications\NewFollower;
use Illuminate\Support\Facades\Event;

it('broadcasts a NotificationCreated signal once a database notification is persisted', function () {
    Event::fake([NotificationCreated::class]);

    $user = User::factory()->withProfile()->create();
    $follower = User::factory()->withProfile()->create();

    $user->notify(new NewFollower($follower));

    Event::assertDispatched(
        NotificationCreated::class,
        fn (NotificationCreated $event) => $event->notifiedUserId === $user->id,
    );
});
