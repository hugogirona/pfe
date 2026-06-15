<?php

use App\Events\ContactPreferenceUpdated;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

it('implements ShouldBroadcastNow for synchronous broadcasting', function () {
    expect(new ContactPreferenceUpdated(1))->toBeInstanceOf(ShouldBroadcastNow::class);
});

it('broadcasts on a private profile channel so anonymous clients cannot subscribe', function () {
    $channels = (new ContactPreferenceUpdated(42))->broadcastOn();

    expect($channels)->toHaveCount(1)
        ->and($channels[0])->toBeInstanceOf(PrivateChannel::class)
        ->and($channels[0]->name)->toBe('private-profile.42');
});

it('uses a short broadcast event name', function () {
    expect((new ContactPreferenceUpdated(1))->broadcastAs())->toBe('contact-preference.updated');
});
