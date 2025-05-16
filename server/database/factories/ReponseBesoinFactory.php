<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReponseBesoin>
 */
class ReponseBesoinFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'etat' => $this->faker->randomElement(['pending', 'accepted', 'rejected']),
            'message' => $this->faker->sentence(),
            'date_reponse' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'valide' => $this->faker->boolean(),
            'demande_id' => \App\Models\Demande::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
