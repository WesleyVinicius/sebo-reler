<?php

namespace Database\Factories;

use App\Models\Autor;
use App\Models\Editora;
use App\Models\Genero;
use App\Models\Livro;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Livro>
 */
class LivroFactory extends Factory
{
    public function definition(): array
    {
        return [
            'titulo' => fake()->sentence(3),
            'id_editora' => Editora::factory(),
            'id_genero' => Genero::factory(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Livro $livro) {
            $livro->autores()->attach(Autor::factory()->create());
        });
    }
}
