<?php

namespace App\Http\Controllers;

use App\Models\Desconto;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class DescontoController extends Controller
{
    public function index()
    {
        $descontos = Desconto::with('funcionario')
            ->orderByDesc('valor_minimo')
            ->get();

        return view('descontos.index', ['descontos' => $descontos]);
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'porcent_desconto' => 'required|numeric|between:0,100',
            'valor_minimo' => 'required|numeric|min:0',
        ]);

        $dados['id_funcionario'] = Auth::guard('funcionario')->id();

        Desconto::create($dados);

        return redirect()->route('descontos.index')->with('sucesso', 'Desconto cadastrado com sucesso.');
    }

    public function update(Request $request, Desconto $desconto)
    {
        $dados = $request->validate([
            'porcent_desconto' => 'required|numeric|between:0,100',
            'valor_minimo' => 'required|numeric|min:0',
        ]);

        $desconto->update($dados);

        return redirect()->route('descontos.index')->with('sucesso', 'Desconto atualizado com sucesso.');
    }

    public function destroy(Desconto $desconto)
    {
        try {
            $desconto->delete();
        } catch (QueryException $e) {
            return redirect()->route('descontos.index')
                ->with('erro', 'Não foi possível excluir este desconto.');
        }

        return redirect()->route('descontos.index')->with('sucesso', 'Desconto excluído com sucesso.');
    }

    public function destroyAll()
    {
        try {
            Desconto::query()->delete();
        } catch (QueryException $e) {
            return redirect()->route('descontos.index')
                ->with('erro', 'Não foi possível excluir todos os descontos.');
        }

        return redirect()->route('descontos.index')->with('sucesso', 'Todos os descontos foram excluídos.');
    }
}
