<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class AutorController extends Controller
{
    public function index(Request $request)
    {
        $busca = $request->query('busca');

        $autores = Autor::when($busca, function ($query, $busca) {
            return $query->where('nome', 'ilike', '%' . $busca . '%');
        })
            ->orderBy('nome', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('autores.index', ['autores' => $autores, 'busca' => $busca]);
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required',
        ]);

        Autor::create($dados);

        return redirect()->route('autores.index')->with('sucesso', 'Autor adicionado com sucesso!');
    }

    public function update(Request $request, Autor $autor)
    {
        $dados = $request->validate([
            'nome' => 'required',
        ]);

        $autor->update($dados);

        return redirect()->route('autores.index')->with('sucesso', 'Autor atualizado com sucesso!');
    }

    public function destroy(Autor $autor)
    {
        try {
            $autor->delete();
        } catch (QueryException $e) {
            return redirect()->route('autores.index')
                ->with('erro', 'Não é possível excluir este autor, existem livros vinculados a ele!');
        }

        return redirect()->route('autores.index')->with('sucesso', 'Autor removido com sucesso!');
    }

    public function destroyAll()
    {
        try {
            Autor::query()->delete();
        } catch (QueryException $e) {
            return redirect()->route('autores.index')
                ->with('erro', 'Não foi possível excluir todos os autores, existem alguns com livros vinculados!');
        }

        return redirect()->route('autores.index')->with('sucesso', 'Autor removido com sucesso!');
    }
}
