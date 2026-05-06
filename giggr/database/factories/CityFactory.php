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
        $postalCode = (string) fake()->numberBetween(1000, 9999);

        return [
            'name' => $name,
            'name_alt' => null,
            'slug' => Str::slug($name).'-'.$postalCode,
            'country' => 'BE',
            'postal_code' => $postalCode,
            'searchable' => City::makeSearchable($name, null, $postalCode),
            'latitude' => fake()->randomFloat(5, 49.5, 51.5),
            'longitude' => fake()->randomFloat(5, 2.5, 6.5),
        ];
    }
}
