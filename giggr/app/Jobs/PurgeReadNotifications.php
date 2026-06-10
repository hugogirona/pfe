<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Notifications\DatabaseNotification;

class PurgeReadNotifications implements ShouldQueue
{
    use Queueable;

    public const int RETENTION_DAYS = 30;

    public function handle(): void
    {
        DatabaseNotification::query()
            ->whereNotNull('read_at')
            ->where('created_at', '<=', now()->subDays(self::RETENTION_DAYS))
            ->delete();
    }
}
