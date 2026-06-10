<?php

use App\Jobs\PurgeReadNotifications;
use App\Models\User;
use App\Notifications\NewFollower;

it('removes read notifications older than a month but keeps recent and unread ones', function () {
    $user = User::factory()->withProfile()->create();
    $follower = User::factory()->withProfile()->create();

    $user->notify(new NewFollower($follower));
    $user->notify(new NewFollower($follower));
    $user->notify(new NewFollower($follower));

    [$oldRead, $recentRead, $oldUnread] = $user->notifications()->get()->all();

    $oldRead->forceFill(['read_at' => now()->subDays(40), 'created_at' => now()->subDays(40)])->save();
    $recentRead->forceFill(['read_at' => now()->subDays(5), 'created_at' => now()->subDays(5)])->save();
    $oldUnread->forceFill(['read_at' => null, 'created_at' => now()->subDays(40)])->save();

    (new PurgeReadNotifications)->handle();

    $ids = $user->fresh()->notifications()->pluck('id');

    expect($ids)->not->toContain($oldRead->id)
        ->and($ids)->toContain($recentRead->id)
        ->and($ids)->toContain($oldUnread->id);
});
