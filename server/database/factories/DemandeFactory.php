<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Demande>
 */
class DemandeFactory extends Factory
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
            'urgent' => $this->faker->boolean(20),
            'quantite' => $this->faker->numberBetween(1, 5),
            'ville' => $this->faker->city(),
            'nom_hopital' => $this->faker->company(),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'completed']),
            'message' => $this->faker->sentence(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
