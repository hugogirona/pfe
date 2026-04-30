<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<City>
 */
class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition(): array
    {
        $name = fake()->unique()->city();

        return [
            'name'      => $name,
            'slug'      => Str::slug($name),
            'country'   => 'BE',
            'latitude'  => fake()->randomFloat(5, 49.5, 51.5),
            'longitude' => fake()->randomFloat(5, 2.5, 6.5),
        ];
    }
}
