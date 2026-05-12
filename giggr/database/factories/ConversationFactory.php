<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conversation>
 */
class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition(): array
    {
        $a = User::factory();
        $b = User::factory();

        return [
            'user_a_id' => $a,
            'user_b_id' => $b,
            'requester_user_id' => $a,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Conversation $convo): void {
            if ($convo->user_a_id > $convo->user_b_id) {
                [$convo->user_a_id, $convo->user_b_id] = [$convo->user_b_id, $convo->user_a_id];
            }
        })->afterCreating(function (Conversation $convo): void {
            $convo->participants()->syncWithoutDetaching([
                $convo->user_a_id,
                $convo->user_b_id,
            ]);
        });
    }

    public function accepted(): static
    {
        return $this->state(fn () => ['accepted_at' => now()]);
    }
}
