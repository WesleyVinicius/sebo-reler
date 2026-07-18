<?php

namespace App\Http\Controllers;

use App\Models\Editora;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class EditoraController extends Controller
{
    public function index(Request $request)
    {
        $busca = $request->query('busca');

        $editoras = Editora::when($busca, function ($query, $busca) {
            $query->where('nome', 'ilike', '%' . $busca . '%');
        })
            ->orderBy('nome', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('editoras.index', ['editoras' => $editoras, 'busca' => $busca]);
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required',
        ]);

        Editora::create($dados);

        return redirect()->route('editoras.index')->with('successo', 'Editora cadastrada com sucesso!');
    }

    public function update(Request $request, Editora $editora)
    {
        $dados = $request->validate([
            'nome' => 'required',
        ]);

        $editora->update($dados);

        return redirect()->route('editoras.index')->with('sucesso', 'Editora atualizada com sucesso!');
    }

    public function destroy(Editora $editora)
    {
        try {
            $editora->delete();
        } catch (QueryException $e) {
            return redirect()->route('editoras.index')
                ->with('erro', 'Não é possível excluir esta editora, existem exemplares vinculadas a ela');
        }

        return redirect()->route('editoras.index')->with('sucesso', 'Editora removida com sucesso!');
    }

    public function destroyAll()
    {
        try {
            Editora::query()->delete();
        } catch (QueryException $e) {
            return redirect()->route('editoras.index')
                ->with('erro', 'Não foi possível excluir todas as editoras, existem algumas que possuem exemplares vinculados!');
        }

        return redirect()->route('editoras.index')->with('sucesso', 'Todos os editoras removidos com sucesso!');
    }
}
