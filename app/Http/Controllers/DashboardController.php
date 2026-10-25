<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Exemplar;
use App\Models\Livro;
use App\Models\Venda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $funcionario = Auth::guard('funcionario')->user();
        $inicioMes = now()->startOfMonth();

        return view('dashboard', [
            'funcionario' => $funcionario,
            'exemplaresDisponiveis' => Exemplar::where('status', true)->count(),
            'totalClientes' => Cliente::count(),
            'vendasMes' => Venda::where('data', '>=', $inicioMes)->count(),
            'faturamentoMes' => (float) Venda::where('data', '>=', $inicioMes)->sum('valor_total'),
            'vendasRecentes' => Venda::with('cliente')
                                    ->withCount('exemplares')
                                    ->orderByDesc('data')
                                    ->take(5)
                                    ->get(),
            'livrosRecentes' => Livro::with('autores')
                                    ->orderByDesc('id')
                                    ->take(5)
                                    ->get(),

        ]);
    }
}
