<?php

namespace Database\Factories;

use App\Enums\AnnouncementStatus;
use App\Enums\AnnouncementType;
use App\Models\Announcement;
use App\Models\City;
use App\Models\Genre;
use App\Models\Instrument;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    public function definition(): array
    {
        $hasExpiry = fake()->boolean(50);

        return [
            'user_id' => User::factory(),
            'city_id' => City::factory(),
            'title' => fake()->sentence(5),
            'description' => fake()->paragraph(),
            'type' => fake()->randomElement(AnnouncementType::cases()),
            'status' => AnnouncementStatus::Open,
            'expires_at' => $hasExpiry ? now()->addDays(fake()->numberBetween(7, 30)) : null,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Announcement $announcement) {
            $instruments = Instrument::inRandomOrder()->limit(fake()->numberBetween(1, 3))->get();
            $genres = Genre::inRandomOrder()->limit(fake()->numberBetween(1, 3))->get();

            if ($instruments->isNotEmpty()) {
                $announcement->instruments()->sync($instruments->pluck('id'));
            }
            if ($genres->isNotEmpty()) {
                $announcement->genres()->sync($genres->pluck('id'));
            }
        });
    }
}
