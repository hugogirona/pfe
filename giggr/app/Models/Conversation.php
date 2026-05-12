<?php

namespace App\Models;

use Database\Factories\ConversationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class Conversation extends Model
{
    /** @use HasFactory<ConversationFactory> */
    use HasFactory;

    protected $fillable = [
        'user_a_id',
        'user_b_id',
        'requester_user_id',
        'accepted_at',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
            'last_message_at' => 'datetime',
        ];
    }

    public static function between(User $a, User $b): self
    {
        if ($a->id === $b->id) {
            throw new InvalidArgumentException('A conversation requires two distinct users.');
        }

        [$low, $high] = $a->id < $b->id ? [$a, $b] : [$b, $a];

        return DB::transaction(function () use ($a, $low, $high): self {
            $conversation = self::firstOrCreate(
                ['user_a_id' => $low->id, 'user_b_id' => $high->id],
                ['requester_user_id' => $a->id],
            );

            if ($conversation->wasRecentlyCreated) {
                $conversation->participants()->attach([$low->id, $high->id]);
            }

            return $conversation;
        });
    }

    public function hasParticipant(User $user): bool
    {
        return $this->participants()->whereKey($user->id)->exists();
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_user')
            ->withPivot(['last_read_at', 'hidden_at']);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)
            ->orderBy('created_at')
            ->orderBy('id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_user_id');
    }

    public function userA(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_a_id');
    }

    public function userB(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_b_id');
    }
}
