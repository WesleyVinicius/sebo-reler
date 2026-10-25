<?php

namespace Tests\Unit;

use App\Services\CalculadoraDesconto;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CalculadoraDescontoTest extends TestCase
{
    private function faixas(): Collection
    {
        return collect([
            (object) ['valor_minimo' => '100.00', 'porcent_desconto' => '5.00'],
            (object) ['valor_minimo' => '250.00', 'porcent_desconto' => '10.00'],
            (object) ['valor_minimo' => '500.00', 'porcent_desconto' => '15.00'],
        ]);
    }

    public static function casosDeDesconto(): array
    {
        // subtotal, % esperado, desconto, total
        return [
            'abaixo de 100' => [80.0,    0.0,  0.0,   80.0],
            'exatamente 100 (sem desconto)' => [100.0,   0.0,  0.0,   100.0],
            'logo acima de 100' => [100.01,  5.0,  5.0,   95.01],
            'logo abaixo de 250' => [249.8,   5.0,  12.49, 237.31],
            'exatamente 250 (ainda 5%)' => [250.0,   5.0,  12.5,  237.5],
            'logo acima de 250' => [250.01, 10.0,  25.0,  225.01],
            'exatamente 500 (ainda 10%)' => [500.0,  10.0,  50.0,  450.0],
            'logo acima de 500' => [500.01, 15.0,  75.0,  425.01],
            'muito acima de 500' => [1000.0, 15.0, 150.0,  850.0],
            'arredonda centavos' => [333.33, 10.0,  33.33, 300.0],
        ];
    }

    #[DataProvider('casosDeDesconto')]
    public function test_calcula_desconto_por_faixa(
        float $subtotal,
        float $percentual,
        float $desconto,
        float $total
    ): void {
        $resultado = CalculadoraDesconto::calcular($subtotal, $this->faixas());

        $this->assertSame($percentual, $resultado['percentual']);
        $this->assertSame($desconto, $resultado['valor_desconto']);
        $this->assertSame($total, $resultado['total']);
    }

    public function test_faixas_desconto(): void
    {
        // Faixas em ordem inversa: o resultado tem que ser o mesmo
        $resultado = CalculadoraDesconto::calcular(600.0, $this->faixas()->reverse());

        $this->assertSame(15.0, $resultado['percentual']);
    }

    public function test_sem_faixas_cadastradas(): void
    {
        $resultado = CalculadoraDesconto::calcular(300.0, collect());

        $this->assertSame(0.0, $resultado['percentual']);
        $this->assertSame(300.0, $resultado['total']);
    }
}
