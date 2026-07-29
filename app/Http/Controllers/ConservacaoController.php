<?php

namespace App\Http\Controllers;

use App\Models\Conservacao;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ConservacaoController extends Controller
{
    // O Estado de Conservação possui uma lista fixa de opções
    public const OPCOES_ESTADO = ['Novo', 'Semi-novo', 'Regular'];

    public function index()
    {
        $conservacoes = Conservacao::orderBy('id')->get();

        return view('conservacoes.index', ['conservacoes' => $conservacoes]);
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'estado' => 'required|string|max:30|unique:conservacao,estado',
        ], [
            'estado.unique' => 'Esse estado de conservação já está cadastrado.',
        ]);

        Conservacao::create($dados);

        return redirect()->route('conservacoes.index')->with('sucesso', 'Estado de conservação cadastrado com sucesso!');
    }

    public function update(Request $request, Conservacao $conservacao)
    {
        $dados = $request->validate([
            'estado' => 'required|string|max:30|unique:conservacao,estado,' . $conservacao->id,
        ], [
            'estado.unique' => 'Esse estado de conservação já está cadastrado.',
        ]);

        $conservacao->update($dados);

        return redirect()->route('conservacoes.index')->with('sucesso', 'Estado de conservação atualizado com sucesso!');
    }

    public function destroy(Conservacao $conservacao)
    {
        try {
            $conservacao->delete();
        } catch (QueryException $e) {
            return redirect()->route('conservacoes.index')->with('erro', 'Não é possível excluir, existem exemplares vinculados a este estado de conservação.');
        }

        return redirect()->route('conservacoes.index')->with('sucesso', 'Estado de conservação excluído com sucesso!');
    }

    public function destroyAll()
    {
        try {
            Conservacao::query()->delete();
        } catch (QueryException $e) {
            return redirect()->route('conservacoes.index')->with('erro', 'Não foi possível excluir todos, alguns possuem exemplares vinculados.');
        }

        return redirect()->route('conservacoes.index')->with('sucesso', 'Todos os estados de conservação foram excluídos');
    }
}
