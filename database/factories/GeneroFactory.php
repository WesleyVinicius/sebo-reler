<?php

namespace Database\Factories;

use App\Models\Genero;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Genero>
 */
class GeneroFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->unique()->word()
        ];
    }
}
