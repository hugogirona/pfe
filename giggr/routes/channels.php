<?php

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function (User $user, int $id) {
    return $user->id === $id;
});

Broadcast::channel('conversation.{conversationId}', function (User $user, int $conversationId) {
    return Conversation::find($conversationId)?->hasParticipant($user) ?? false;
});
