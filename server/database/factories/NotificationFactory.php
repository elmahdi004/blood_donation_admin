<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['info', 'alert', 'reminder']),
            'message' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['unread', 'read']),
            'date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
