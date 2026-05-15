<?php

namespace App\Models;

use Database\Factories\CityFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class City extends Model
{
    /** @use HasFactory<CityFactory> */
    use HasFactory;

    /**
     * Earth's mean radius in kilometers, used as the multiplier in the
     * haversine formula further in the code.
     */
    private const int EARTH_RADIUS_KM = 6371;

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

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn (): string => "$this->name ($this->postal_code)",
        );
    }

    public static function makeSearchable(string $name, ?string $alt, string $postal): string
    {
        return implode(' ', array_filter([
            Str::slug($name),
            $alt !== null ? Str::slug($alt) : '',
            $postal,
        ], fn (string $part) => $part !== ''));
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    /**
     * @noinspection PhpParamsInspection because of the type hinting, whereRaw actually accepts a string
     */
    #[Scope]
    protected function nearby(Builder $query, float $lat, float $lng, float $radiusKm): void
    {
        // I had to add CAST(? AS REAL) because in SQLite the radius value
        // was being treated as text, so the comparison was always true and
        // every row was returned. Wrapping it in CAST fixed the tests. f--- SQLite.
        $query->whereRaw(
            self::haversineKmSql().' <= CAST(? AS REAL)',
            [$lat, $lng, $lat, $radiusKm],
        );
    }

    #[Scope]
    protected function orderByDistance(Builder $query, float $lat, float $lng): void
    {
        $query->orderByRaw(
            self::haversineKmSql().' asc',
            [$lat, $lng, $lat],
        );
    }

    /**
     * Haversine formula, see https://en.wikipedia.org/wiki/Haversine_formula
     */
    private static function haversineKmSql(): string
    {
        $r = self::EARTH_RADIUS_KM;

        return "($r * acos("
            .'cos(radians(?)) * cos(radians(latitude))'
            .' * cos(radians(longitude) - radians(?))'
            .' + sin(radians(?)) * sin(radians(latitude))'
            .'))';
    }
}
