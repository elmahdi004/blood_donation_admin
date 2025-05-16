<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AvailableSlot>
 */
class AvailableSlotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('+1 days', '+10 days');
        $end = (clone $start)->modify('+1 hour');
        return [
            'centre_id' => \App\Models\User::factory(),
            'start_time' => $start,
            'end_time' => $end,
            'is_booked' => false,
        ];
    }
}
