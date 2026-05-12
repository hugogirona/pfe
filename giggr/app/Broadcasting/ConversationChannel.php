<?php

namespace App\Broadcasting;

use App\Models\User;

class ConversationChannel
{
    public function join(User $user, int $conversationId): bool
    {
        return $user->conversations()->whereKey($conversationId)->exists();
    }
}
