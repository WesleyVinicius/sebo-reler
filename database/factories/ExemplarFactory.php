<?php

namespace Database\Factories;

use App\Models\Conservacao;
use App\Models\Exemplar;
use App\Models\Funcionario;
use App\Models\Livro;
use App\Models\Venda;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Exemplar>
 */
class ExemplarFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_livro' => Livro::factory(),
            'id_conservacao' => Conservacao::factory(),
            'id_venda' => null,
            'id_funcionario' => Funcionario::factory(),
            'preco_compra' => 10,
            'preco_venda' => 25,
            'status' => true,
        ];
    }

    public function vendido(): static
    {
        return $this->state(fn () => [
            'id_venda' => Venda::factory(),
            'status' => false,
        ]);
    }
}
