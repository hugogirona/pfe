<?php

namespace App\Actions;

use App\Models\User;
use InvalidArgumentException;

class HideConversation
{
    public function execute(User $user, int $conversationId): void
    {
        if (! $user->conversations()->whereKey($conversationId)->exists()) {
            throw new InvalidArgumentException('User does not participate in this conversation.');
        }

        $user->conversations()->updateExistingPivot($conversationId, [
            'hidden_at' => now(),
        ]);
    }
}
