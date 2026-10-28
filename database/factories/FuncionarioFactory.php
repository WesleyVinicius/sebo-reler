<?php

namespace Database\Factories;

use App\Models\Funcionario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Funcionario>
 */
class FuncionarioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'usuario' => fake()->unique()->numerify('########'),
            'senha' => Hash::make('senha123'),
            'cpf' => fake()->unique()->numerify('###########'),
            'rg' => fake()->unique()->numerify('#########'),
            'sexo' => fake()->randomElement(['Feminino', 'Masculino', 'Outro']),
            'data_nascimento' => fake()->date('Y-m-d'),
            'salario' => 3000,
            'perfil' => 'VENDEDOR',
        ];
    }

    public function gerente(): static
    {
        return $this->state(fn () => ['perfil' => 'GERENTE']);
    }
}
