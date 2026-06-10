<?php

namespace App\Listeners;

use App\Events\NotificationCreated;
use App\Models\User;
use Illuminate\Notifications\Events\NotificationSent;
use Throwable;

class BroadcastNotificationCreated
{
    public function handle(NotificationSent $event): void
    {
        if ($event->channel !== 'database') {
            return;
        }

        if (! $event->notifiable instanceof User) {
            return;
        }

        // Best-effort: a websocket outage must never fail the persisted notification.
        try {
            NotificationCreated::dispatch($event->notifiable->id);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
