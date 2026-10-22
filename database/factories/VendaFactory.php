<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Funcionario;
use App\Models\Venda;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Venda>
 */
class VendaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_funcionario' => Funcionario::factory(),
            'id_cliente' => Cliente::factory(),
            'valor_total' => 25,
            'valor_desconto' => 0,
        ];
    }
}
