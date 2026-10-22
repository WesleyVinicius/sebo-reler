<?php

namespace Database\Factories;

use App\Models\Conservacao;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conservacao>
 */
class ConservacaoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'estado' => fake()->unique()->word()
        ];
    }
}
