<?php

namespace App\Http\Controllers;

use App\Models\Exemplar;
use App\Models\Venda;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class RelatorioController extends Controller
{
    public function vendas(Request $request)
    {
        [$inicio, $fim] = $this->periodo($request);

        $vendas = $this->vendasDoPeriodo($inicio, $fim)
            ->with(['cliente', 'funcionario'])
            ->withCount('exemplares')
            ->orderByDesc('data')
            ->paginate(15)
            ->withQueryString();

        return view('relatorios.vendas', [
            ...$this->totais($inicio, $fim),
            'vendas' => $vendas,
            'inicio' => $inicio->format('Y-m-d'),
            'fim' => $fim->format('Y-m-d'),
        ]);
    }

    public function imprimir(Request $request)
    {
        [$inicio, $fim] = $this->periodo($request);

        $vendas = $this->vendasDoPeriodo($inicio, $fim)
            ->with(['cliente', 'funcionario'])
            ->withCount('exemplares')
            ->orderBy('data')
            ->get();

        return view('relatorios.vendas-imprimir', [
            ...$this->totais($inicio, $fim),
            'vendas' => $vendas,
            'inicio' => $inicio,
            'fim' => $fim,
            'geradoPor' => Auth::guard('funcionario')->user()->nome,
            'geradoEm' => now(),
        ]);
    }

    // Valida os filtros e devolve [início, fim] como datas Carbon
    private function periodo(Request $request): array
    {
        $dados = $request->validate([
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
        ], [
            'data_fim.after_or_equal' => 'A data final não pode ser anterior à data inicial.',
        ]);

        $inicio = Carbon::parse($dados['data_inicio'] ?? now()->startOfMonth())->startOfDay();
        $fim = Carbon::parse($dados['data_fim'] ?? now())->endOfDay();

        return [$inicio, $fim];
    }

    private function vendasDoPeriodo(Carbon $inicio, Carbon $fim)
    {
        return Venda::whereBetween('data', [$inicio, $fim]);
    }

    // Resumo geral + total por vendedor
    private function totais(Carbon $inicio, Carbon $fim): array
    {
        $descontos = (float) $this->vendasDoPeriodo($inicio, $fim)->sum('valor_desconto');
        $liquido = (float) $this->vendasDoPeriodo($inicio, $fim)->sum('valor_total');

        return [
            'resumo' => [
                'quantidade' => $this->vendasDoPeriodo($inicio, $fim)->count(),
                'itens' => Exemplar::whereIn('id_venda', $this->vendasDoPeriodo($inicio, $fim)->select('id'))->count(),
                'bruto' => $liquido + $descontos,
                'descontos' => $descontos,
                'liquido' => $liquido,
            ],
            'porVendedor' => $this->vendasDoPeriodo($inicio, $fim)
                ->selectRaw('id_funcionario, COUNT(*) as quantidade, SUM(valor_total) as total')
                ->groupBy('id_funcionario')
                ->orderByDesc('total')
                ->with('funcionario')
                ->get(),
        ];
    }
}
