<?php

namespace Database\Factories;

use App\Enums\MediaType;
use App\Models\Media;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition(): array
    {
        return [
            'profile_id' => Profile::factory(),
            'type' => MediaType::Image,
            'source' => 'gallery-photo-'.Str::random(8),
            'position' => 0,
            'caption' => null,
            'width' => fake()->numberBetween(800, 2400),
            'height' => fake()->numberBetween(600, 1800),
        ];
    }

    public function image(): static
    {
        return $this->state(fn () => [
            'type' => MediaType::Image,
            'source' => 'gallery-photo-'.Str::random(8),
            'width' => fake()->numberBetween(800, 2400),
            'height' => fake()->numberBetween(600, 1800),
        ]);
    }

    public function youtube(): static
    {
        return $this->state(fn () => [
            'type' => MediaType::Youtube,
            'source' => Str::random(11),
            'width' => null,
            'height' => null,
        ]);
    }
}
