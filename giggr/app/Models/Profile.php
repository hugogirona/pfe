<?php

namespace App\Models;

use App\Enums\ContactPreference;
use App\Enums\ProfileStatus;
use App\Models\Concerns\Searchable;
use Database\Factories\ProfileFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Profile extends Model
{
    /** @use HasFactory<ProfileFactory> */
    use HasFactory, Searchable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'city_id',
        'bio',
        'birth_date',
        'avatar_path',
        'status',
        'contact_preference',
        'experience_years',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProfileStatus::class,
            'contact_preference' => ContactPreference::class,
            'birth_date' => 'date',
        ];
    }

    /**
     * @return array<int|string, string|list<string>>
     */
    protected function searchable(): array
    {
        return [
            'user' => ['first_name', 'last_name'],
            'instruments' => ['name'],
            'genres' => ['name'],
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

    public function media(): HasMany
    {
        return $this->hasMany(Media::class)
            ->orderBy('position')
            ->orderBy('id');
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

    protected function experienceYearsLabel(): Attribute
    {
        return Attribute::make(
            get: fn (): string => match (true) {
                $this->experience_years <= 0 => __('profile.experience_unset_short'),
                $this->experience_years >= 15 => '15+',
                default => (string) $this->experience_years,
            },
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

    private function avatarVariantUrl(string $variant): ?string
    {
        if (! $this->avatar_path) {
            return null;
        }

        $baseDir = config('avatars.base_dir');

        return Storage::disk(config('avatars.disk'))
            ->url("{$baseDir}/{$variant}/{$this->avatar_path}.webp");
    }
}
