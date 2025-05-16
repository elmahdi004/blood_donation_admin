<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IntentionDon>
 */
class IntentionDonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'groupe_sanguin' => $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'date_disponibilite' => $this->faker->dateTimeBetween('now', '+1 month'),
            'valide' => $this->faker->boolean(),
            'notes' => $this->faker->sentence(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
