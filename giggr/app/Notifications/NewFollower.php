<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Notification;

class NewFollower extends Notification
{
    public function __construct(public User $follower) {}

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
        return [
            'follower_user_id' => $this->follower->id,
            'follower_profile_id' => $this->follower->profile?->id,
            'follower_name' => $this->follower->full_name,
            'follower_thumbnail' => $this->follower->profile?->thumbnail,
        ];
    }
}
