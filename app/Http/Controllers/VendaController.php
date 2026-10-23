<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Desconto;
use App\Models\Exemplar;
use App\Models\Venda;
use App\Services\CalculadoraDesconto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VendaController extends Controller
{
    public function index()
    {
        $vendas = Venda::with(['cliente', 'funcionario'])
            ->withCount('exemplares')
            ->orderByDesc('data')
            ->paginate(10);

        return view('vendas.index', compact('vendas'));
    }

    public function create()
    {
        $faixas = Desconto::orderBy('valor_minimo')->get(['valor_minimo', 'porcent_desconto']);

        return view('vendas.create', compact('faixas'));
    }

    public function buscarCliente(string $cpf)
    {
        // Aceita "123.456.789-00" ou "12345678900": comparamos só os dígitos
        $digitos = preg_replace('/\D/', '', $cpf);

        $cliente = Cliente::whereRaw("regexp_replace(cpf, '\\D', '', 'g') = ?", [$digitos])->first();

        if (! $cliente) {
            return response()->json(['mensagem' => 'Cliente não encontrado.'], 404);
        }

        return response()->json([
            'id'   => $cliente->id,
            'nome' => $cliente->nome,
            'cpf'  => $cliente->cpf,
        ]);
    }

    public function buscarExemplar(int $id)
    {
        $exemplar = Exemplar::with(['livro.autores', 'conservacao'])->find($id);

        if (! $exemplar) {
            return response()->json(['mensagem' => 'Exemplar não encontrado.'], 404);
        }

        if (! $exemplar->status) {
            return response()->json(['mensagem' => 'Este exemplar já foi vendido.'], 422);
        }

        return response()->json([
            'id'          => $exemplar->id,
            'titulo'      => $exemplar->livro->titulo,
            'autores'     => $exemplar->livro->autores->pluck('nome')->join(', '),
            'conservacao' => $exemplar->conservacao->estado,
            'preco'       => (float) $exemplar->preco_venda,
        ]);
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'id_cliente'    => 'required|exists:cliente,id',
            'exemplares'    => 'required|array|min:1',
            'exemplares.*'  => 'integer|distinct',
        ], [
            'id_cliente.required' => 'Selecione o cliente.',
            'exemplares.required' => 'Selecione ao menos um exemplar.',
            'exemplares.min'      => 'Selecione ao menos um exemplar.',
        ]);

        $venda = DB::transaction(function () use ($dados) {
            // Trava as linhas escolhidas e só aceita as que ainda estão disponíveis
            $exemplares = Exemplar::whereIn('id', $dados['exemplares'])
                ->where('status', true)
                ->lockForUpdate()
                ->get();

            if ($exemplares->count() !== count($dados['exemplares'])) {
                throw ValidationException::withMessages([
                    'exemplares' => 'Algum exemplar selecionado não está mais disponível.',
                ]);
            }

            $subtotal = (float) $exemplares->sum('preco_venda');
            $calculo  = CalculadoraDesconto::calcular($subtotal, Desconto::all());

            $venda = Venda::create([
                'id_funcionario' => Auth::guard('funcionario')->id(),
                'id_cliente'     => $dados['id_cliente'],
                'valor_total'    => $calculo['total'],
                'valor_desconto' => $calculo['valor_desconto'],
            ]);

            // A trigger do banco marca status = false ao receber id_venda
            Exemplar::whereIn('id', $exemplares->pluck('id'))
                ->update(['id_venda' => $venda->id]);

            return $venda;
        });

        return redirect()->route('vendas.show', $venda)
            ->with('sucesso', 'Venda registrada com sucesso!');
    }

    public function show(Venda $venda)
    {
        $venda->load(['cliente', 'funcionario', 'exemplares.livro', 'exemplares.conservacao']);

        $subtotal = $venda->valor_total + $venda->valor_desconto;

        return view('vendas.show', compact('venda', 'subtotal'));
    }
}
