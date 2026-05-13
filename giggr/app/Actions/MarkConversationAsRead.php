<?php

namespace App\Actions;

use App\Events\MessagesRead;
use App\Models\Message;
use App\Models\User;
use Throwable;

class MarkConversationAsRead
{
    public function execute(User $reader, int $conversationId): void
    {
        $unreadIds = Message::query()
            ->where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $reader->id)
            ->whereNull('read_at')
            ->pluck('id')
            ->all();

        if ($unreadIds !== []) {
            Message::query()
                ->whereIn('id', $unreadIds)
                ->update(['read_at' => now()]);

            try {
                broadcast(new MessagesRead($conversationId, $reader->id, $unreadIds))->toOthers();
            } catch (Throwable $e) {
                // A broadcast failure (Reverb offline etc.) must not break the
                // calling flow — the read state is already persisted.
                report($e);
            }
        }

        $reader->conversations()->updateExistingPivot($conversationId, [
            'last_read_at' => now(),
        ]);
    }
}
