<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\Follow;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Follow>
 */
class FollowFactory extends Factory
{
    protected $model = Follow::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'followable_type' => null,
            'followable_id' => null,
        ];
    }

    public function configure(): static
    {
        return $this
            ->afterMaking(function (Follow $follow): void {
                $followable = fake()->boolean(50)
                    ? Profile::factory()->make()
                    : Announcement::factory()->make();

                $follow->followable_type = $followable->getMorphClass();
                $follow->followable_id = $followable->getKey();
                $follow->setRelation('followable', $followable);
            })
            ->afterCreating(function (Follow $follow): void {
                $followable = fake()->boolean(50)
                    ? Profile::factory()->create()
                    : Announcement::factory()->create();

                $follow->forceFill([
                    'followable_type' => $followable->getMorphClass(),
                    'followable_id' => $followable->getKey(),
                ])->save();

                $follow->setRelation('followable', $followable);
            });
    }
}
