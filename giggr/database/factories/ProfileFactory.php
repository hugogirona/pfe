<?php

namespace Database\Factories;

use App\Enums\ProfileStatus;
use App\Models\City;
use App\Models\Genre;
use App\Models\Instrument;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'city_id' => fn () => City::inRandomOrder()->value('id') ?? City::factory(),
            'bio' => fake()->paragraph(),
            'birth_date' => fake()->dateTimeBetween('-55 years', '-18 years')->format('Y-m-d'),
            'avatar_path' => null,
            'status' => fake()->randomElement(ProfileStatus::cases()),
            'experience_years' => fake()->numberBetween(0, 30),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Profile $profile) {
            $instruments = Instrument::inRandomOrder()->limit(fake()->numberBetween(1, 3))->get();
            $genres = Genre::inRandomOrder()->limit(fake()->numberBetween(1, 3))->get();

            if ($instruments->isNotEmpty()) {
                $profile->instruments()->sync($instruments->pluck('id'));
            }
            if ($genres->isNotEmpty()) {
                $profile->genres()->sync($genres->pluck('id'));
            }
        });
    }
}
