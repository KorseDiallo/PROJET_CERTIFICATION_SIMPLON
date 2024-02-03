<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\collecteDeFond>
 */
class collecteDeFondFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titre' => $this->faker->word,
            'description' => $this->faker->paragraph,
            'image' => $this->faker->url(),
            // 'image' => 'path/to/your/image.jpg', 
            'objectifFinancier' => $this->faker->randomNumber(4),
            'numeroCompte' => $this->faker->numerify('########'),
            'user_id' => function () {
                return \App\Models\User::factory()->create()->id;
            },
        ];
    }
}
