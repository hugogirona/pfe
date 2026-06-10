<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewAnnouncement extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Announcement $announcement) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $author = $this->announcement->user;

        return [
            'actor_user_id' => $author->id,
            'actor_profile_id' => $author->profile?->id,
            'actor_name' => $author->full_name,
            'announcement_id' => $this->announcement->id,
            'announcement_title' => $this->announcement->title,
        ];
    }
}
