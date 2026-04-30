<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\Favorite;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Favorite>
 */
class FavoriteFactory extends Factory
{
    protected $model = Favorite::class;

    public function definition(): array
    {
        $favoritable = fake()->boolean(50)
            ? Profile::factory()->create()
            : Announcement::factory()->create();

        return [
            'user_id' => User::factory(),
            'favoritable_type' => $favoritable->getMorphClass(),
            'favoritable_id' => $favoritable->id,
        ];
    }
}
