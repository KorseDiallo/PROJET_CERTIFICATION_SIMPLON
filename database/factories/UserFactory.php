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
        return [
            'nom' => $this->faker->name ,
            'prenom' => $this->faker->lastName ,
            'image' =>  $this->faker->url() ,
            'description' => $this->faker->sentence ,
            'numeroEnregistrement' => $this->faker->numerify('#####') ,
            'adresse' => $this->faker->address ,
            'email' => $this->faker->unique()->safeEmail() ,
            'email_verified_at' => now(),
            'password' => bcrypt('password'), 
            'telephone' => $this->faker->phoneNumber,
            'role' => 'admin',
            // 'role' => 'fondation',
            // 'remember_token' => Str::random(10),
            'statut' => 'accepte',
            'bloque' => false,
            // 'bloque' => $this->faker->boolean,
            'is_deleted' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function donneur()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'donateur',
            ];
        });
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
