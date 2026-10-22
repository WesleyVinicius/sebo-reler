<?php

namespace Database\Factories;

use App\Models\Editora;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Editora>
 */
class EditoraFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->company()
        ];
    }
}
