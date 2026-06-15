<?php

use App\Broadcasting\ConversationChannel;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function (User $user, int $id) {
    return $user->id === $id;
});

// Carries no payload beyond the profile id and only signals a UI refresh of the
// contact button. Profiles are visible to any authenticated member, so any
// signed-in user may listen; guests (who never reach a profile page) cannot.
Broadcast::channel('profile.{profileId}', fn (User $user) => true);

Broadcast::channel('conversation.{conversationId}', ConversationChannel::class);
