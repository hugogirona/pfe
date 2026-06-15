<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    public function view(User $user, Conversation $conversation): bool
    {
        return $this->isParticipant($user, $conversation);
    }

    public function delete(User $user, Conversation $conversation): bool
    {
        return $this->isParticipant($user, $conversation);
    }

    public function respondToRequest(User $user, Conversation $conversation): bool
    {
        return $this->isParticipant($user, $conversation)
            && (int) $conversation->requester_user_id !== $user->id;
    }

    private function isParticipant(User $user, Conversation $conversation): bool
    {
        return $conversation->participants()->whereKey($user->id)->exists();
    }
}
