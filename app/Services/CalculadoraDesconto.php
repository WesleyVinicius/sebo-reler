<?php

namespace App\Services;

use Illuminate\Support\Collection;

class CalculadoraDesconto
{
    public static function calcular(float $subtotal, Collection $faixas): array
    {
        $faixa = $faixas
            ->filter(fn ($f) => $subtotal > (float) $f->valor_minimo)
            ->sortByDesc(fn ($f) => (float) $f->valor_minimo)
            ->first();

        $percentual = $faixa ? (float) $faixa->porcent_desconto : 0.0;
        $valorDesconto = round($subtotal * $percentual / 100, 2);

        return [
            'percentual' => $percentual,
            'valor_desconto' => $valorDesconto,
            'total' => round($subtotal - $valorDesconto, 2),
        ];
    }
}
