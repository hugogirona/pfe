<?php

namespace App\Models;

use App\Concerns\HandlesAvatar;
use App\Enums\ProfileStatus;
use Database\Factories\ProfileFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    /** @use HasFactory<ProfileFactory> */
    use HandlesAvatar, HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'city_id',
        'bio',
        'birth_date',
        'avatar_path',
        'status',
        'experience_years',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProfileStatus::class,
            'birth_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function instruments(): BelongsToMany
    {
        return $this->belongsToMany(Instrument::class);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function followers(): MorphMany
    {
        return $this->morphMany(Follow::class, 'followable');
    }

    public function followed(): HasMany
    {
        return $this->hasMany(Follow::class, 'user_id', 'user_id')
            ->where('followable_type', 'profile');
    }

    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->birth_date?->age,
        );
    }

    protected function thumbnail(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->avatarVariantUrl('thumbnail'),
        );
    }

    protected function medium(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->avatarVariantUrl('medium'),
        );
    }
}
