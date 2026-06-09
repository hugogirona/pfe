<?php

namespace App\Models;

use App\Enums\ContactPreference;
use App\Events\ConversationClosed;
use App\Notifications\PasswordResetLink;
use App\Notifications\WelcomeWithVerificationCode;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use InvalidArgumentException;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'email_verification_code',
        'email_verification_code_expires_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'email_verification_code_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sendEmailVerificationNotification(): void
    {
        if ($this->email_verification_code === null) {
            return;
        }

        $code = $this->email_verification_code;
        dispatch(function () use ($code) {
            $this->notify(new WelcomeWithVerificationCode($code));
        })->afterResponse();
    }

    public function sendPasswordResetNotification($token): void
    {
        dispatch(function () use ($token) {
            $this->notify(new PasswordResetLink($token));
        })->afterResponse();
    }

    protected function firstName(): Attribute
    {
        return Attribute::make(
            set: fn (string $value): string => Str::ucfirst(trim($value)),
        );
    }

    protected function lastName(): Attribute
    {
        return Attribute::make(
            set: fn (string $value): string => Str::ucfirst(trim($value)),
        );
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->first_name.' '.$this->last_name,
        );
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function follows(): HasMany
    {
        return $this->hasMany(Follow::class);
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user')
            ->withPivot(['last_read_at', 'hidden_at']);
    }

    public function unreadConversationsCount(): int
    {
        return Conversation::query()
            ->whereHas('participants', function ($q): void {
                $q->where('users.id', $this->id)
                    ->where('conversation_user.hidden_at', null);
            })
            ->whereHas('messages', function ($q): void {
                $q->where('sender_id', '!=', $this->id)
                    ->whereNull('read_at');
            })
            ->count();
    }

    /**
     * @return array<int, int>
     */
    public function followedProfileIds(): array
    {
        return $this->follows()
            ->where('followable_type', 'profile')
            ->pluck('followable_id')
            ->all();
    }

    public function follow(Model $followable): Follow
    {
        return $this->follows()->firstOrCreate([
            'followable_type' => $followable->getMorphClass(),
            'followable_id' => $followable->getKey(),
        ]);
    }

    public function unfollow(Model $followable): void
    {
        $this->follows()
            ->where('followable_type', $followable->getMorphClass())
            ->where('followable_id', $followable->getKey())
            ->delete();
    }

    public function isFollowing(Model $followable): bool
    {
        return $this->follows()
            ->where('followable_type', $followable->getMorphClass())
            ->where('followable_id', $followable->getKey())
            ->exists();
    }

    public function canBeContactedBy(User $viewer): bool
    {
        $preference = $this->profile?->contact_preference ?? ContactPreference::Everyone;

        return match ($preference) {
            ContactPreference::Everyone => true,
            ContactPreference::FollowersOnly => $viewer->profile !== null && $this->isFollowing($viewer->profile),
            ContactPreference::Nobody => false,
        };
    }

    public function blockedUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            related: self::class,
            table: 'user_blocks',
            foreignPivotKey: 'blocker_user_id',
            relatedPivotKey: 'blocked_user_id',
        )->withTimestamps();
    }

    public function block(User $target): void
    {
        if ($this->id === $target->id) {
            throw new InvalidArgumentException('A user cannot block themselves.');
        }

        $this->blockedUsers()->syncWithoutDetaching([$target->id]);
        $existing = $this->conversations()
            ->whereHas('participants', fn ($q) => $q->whereKey($target->id))
            ->first();
        if ($existing !== null) {
            $this->conversations()->updateExistingPivot($existing->id, [
                'hidden_at' => now(),
            ]);

            try {
                broadcast(new ConversationClosed((int) $existing->id));
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    public function unblock(User $target): void
    {
        $this->blockedUsers()->detach($target->id);
    }

    public function hasBlocked(User $other): bool
    {
        return $this->blockedUsers()->whereKey($other->id)->exists();
    }

    public function isBlockedBy(User $other): bool
    {
        return $other->hasBlocked($this);
    }
}
