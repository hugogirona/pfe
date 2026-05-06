<?php

namespace App\Models;

use Database\Factories\CityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class City extends Model
{
    /** @use HasFactory<CityFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'name_alt',
        'slug',
        'country',
        'postal_code',
        'searchable',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function getDisplayNameAttribute(): string
    {
        return "{$this->name} ({$this->postal_code})";
    }

    public static function makeSearchable(string $name, ?string $alt, string $postal): string
    {
        return trim(Str::slug($name).' '.Str::slug($alt ?? '').' '.$postal);
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }
}
