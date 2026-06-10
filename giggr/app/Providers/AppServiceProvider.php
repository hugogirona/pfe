<?php

namespace App\Providers;

use App\Listeners\BroadcastNotificationCreated;
use App\Models\Announcement;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Relation::enforceMorphMap([
            'profile' => Profile::class,
            'announcement' => Announcement::class,
            'user' => User::class,
        ]);

        Event::listen(NotificationSent::class, BroadcastNotificationCreated::class);
    }
}
