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
        return [
            'user_id' => User::factory(),
            'favoritable_type' => null,
            'favoritable_id' => null,
        ];
    }

    public function configure(): static
    {
        return $this
            ->afterMaking(function (Favorite $favorite): void {
                $favoritable = fake()->boolean(50)
                    ? Profile::factory()->make()
                    : Announcement::factory()->make();

                $favorite->favoritable_type = $favoritable->getMorphClass();
                $favorite->favoritable_id = $favoritable->getKey();
                $favorite->setRelation('favoritable', $favoritable);
            })
            ->afterCreating(function (Favorite $favorite): void {
                $favoritable = fake()->boolean(50)
                    ? Profile::factory()->create()
                    : Announcement::factory()->create();

                $favorite->forceFill([
                    'favoritable_type' => $favoritable->getMorphClass(),
                    'favoritable_id' => $favoritable->getKey(),
                ])->save();

                $favorite->setRelation('favoritable', $favoritable);
            });
    }
}
