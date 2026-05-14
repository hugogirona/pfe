<?php

namespace App\Actions;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Throwable;

class SendMessage
{
    /**
     * @throws Throwable
     */
    public function execute(User $sender, User $recipient, string $body): Message
    {
        if ($sender->id === $recipient->id) {
            throw new InvalidArgumentException('A user cannot send a message to themselves.');
        }

        if ($sender->hasBlocked($recipient) || $sender->isBlockedBy($recipient)) {
            throw new InvalidArgumentException('Messages cannot be exchanged between blocked users.');
        }

        Validator::make(['body' => $body], [
            'body' => ['required', 'string', 'min:1', 'max:2000'],
        ])->validate();

        $message = DB::transaction(function () use ($sender, $recipient, $body): Message {
            $conversation = Conversation::between($sender, $recipient);
            $conversation = Conversation::query()
                ->whereKey($conversation->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $message = $conversation->messages()->create([
                'sender_id' => $sender->id,
                'body' => $body,
            ]);

            $timestamp = now();
            $updates = ['last_message_at' => $timestamp];

            if ($conversation->accepted_at === null && $this->shouldAutoAccept($conversation, $sender, $recipient)) {
                $updates['accepted_at'] = $timestamp;
            }

            $conversation->update($updates);

            $conversation->participants()->updateExistingPivot($recipient->id, [
                'hidden_at' => null,
            ]);
            $conversation->participants()->updateExistingPivot($sender->id, [
                'hidden_at' => null,
            ]);

            return $message;
        });

        try {
            broadcast(new MessageSent($message))->toOthers();
        } catch (Throwable $e) {
            report($e);
        }

        return $message;
    }

    private function shouldAutoAccept(Conversation $conversation, User $sender, User $recipient): bool
    {
        if ($sender->id !== $conversation->requester_user_id) {
            return true;
        }

        return $sender->profile !== null && $recipient->isFollowing($sender->profile);
    }
}
