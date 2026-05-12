<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'sender_id' => null,
            'body' => fake()->sentence(),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Message $message): void {
            if ($message->sender_id !== null) {
                return;
            }
            $conversation = Conversation::find($message->conversation_id);
            if ($conversation === null) {
                return;
            }
            $message->sender_id = fake()->randomElement([
                $conversation->user_a_id,
                $conversation->user_b_id,
            ]);
        });
    }

    public function read(): static
    {
        return $this->state(fn () => ['read_at' => now()]);
    }
}
