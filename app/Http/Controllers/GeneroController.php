<?php

namespace App\Http\Controllers;

use App\Models\Genero;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class GeneroController extends Controller
{
    public function index(Request $request)
    {
        $busca = $request->query('busca');

        $generos = Genero::when($busca, function ($query, $busca) {
            $query->where('nome', 'ilike', '%' . $busca . '%');
        })
            ->orderBy('nome', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('generos.index', ['generos' => $generos, 'busca' => $busca]);
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:60',
        ]);

        Genero::create($dados);

        return redirect()->route('generos.index')->with('sucesso', 'Genero cadastrado com sucesso!');
    }

    public function update(Request $request, Genero $genero)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:60',
        ]);

        $genero->update($dados);

        return redirect()->route('generos.index')->with('sucesso', 'Genero atualizado com sucesso!');
    }

    public function destroy(Genero $genero)
    {
        try {
            $genero->delete();
        } catch (QueryException $e) {
            return redirect()->route('generos.index')
                ->with('erro', 'Não é possível excluir este genero, existem exemplares vinculados a este ele!');
        }

        return redirect()->route('generos.index')->with('sucesso', 'Genero excluído com sucesso!');
    }

    public function destroyAll()
    {
        try {
            Genero::query()->delete();
        } catch (QueryException $e) {
            return redirect()->route('generos.index')
                ->with('erro', 'Não foi possível excluir todos gêneros, existem exemplares vinculados a alguns deles');
        }

        return redirect()->route('generos.index')->with('sucesso', 'Todos os gêneros foram excluídos"');
    }
}
