<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proposition>
 */
class PropositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'message' => $this->faker->sentence(),
            'demande_id' => \App\Models\Demande::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
