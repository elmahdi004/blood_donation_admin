<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['utilisateur', 'centre_de_don', 'admin'];
        $type = $this->faker->randomElement($types);
        return [
            'nom' => $this->faker->lastName(),
            'prenom' => $type === 'utilisateur' ? $this->faker->firstName() : null,
            'email' => $this->faker->unique()->safeEmail(),
            'date_de_naissance' => $type === 'utilisateur' ? $this->faker->date() : null,
            'telephone' => $this->faker->phoneNumber(),
            'password' => static::$password ??= Hash::make('password'),
            'type' => $type,
            'groupe_sanguin' => $type === 'utilisateur' ? $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']) : null,
            'adresse' => $this->faker->address(),
            'status' => $type === 'centre_de_don' ? $this->faker->randomElement(['active', 'inactive']) : null,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
