<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use App\Models\Conservacao;
use App\Models\Editora;
use App\Models\Exemplar;
use App\Models\Genero;
use App\Models\Livro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LivroController extends Controller
{
    public function index(Request $request)
    {
        $busca = $request->query('busca');

        // Cada LINHA da listagem representa um GRUPO de exemplares que compartilham
        // livro + conservação + preço + status. Se dois exemplares do mesmo livro
        // têm preço ou conservação diferentes, caem em grupos (linhas) separados.
        $query = Exemplar::selectRaw('MIN(id) as id, id_livro, id_conservacao, preco_compra, preco_venda, status, COUNT(*) as quantidade')
            ->groupBy('id_livro', 'id_conservacao', 'preco_compra', 'preco_venda', 'status');

        if ($busca) {
            $query->whereHas('livro', function ($q) use ($busca) {
                $q->where('titulo', 'ilike', "%{$busca}%");
            });
        }

        $grupos = $query->orderByDesc('id')->paginate(10)->withQueryString();

        // with() não funciona antes de um groupBy, então carregamos os relacionamentos
        // depois de já ter os grupos em mãos.
        $grupos->load(['livro.autores', 'livro.genero', 'livro.editora', 'conservacao']);

        return view('livros.index', [
            'grupos' => $grupos,
            'busca' => $busca,
            'autores' => Autor::orderBy('nome')->get(),
            'editoras' => Editora::orderBy('nome')->get(),
            'generos' => Genero::orderBy('nome')->get(),
            'conservacoes' => Conservacao::orderBy('estado')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'titulo' => 'required|string|max:255',
            'autores' => 'required|array|min:1',
            'autores.*' => 'exists:autor,id',
            'id_editora' => 'required|exists:editora,id',
            'id_genero' => 'required|exists:genero,id',
            'id_conservacao' => 'required|exists:conservacao,id',
            'preco_compra' => 'required|numeric|min:0',
            'preco_venda' => 'required|numeric|min:0',
            'quantidade' => 'required|integer|min:1|max:100',
        ]);

        DB::transaction(function () use ($dados) {
            $livro = Livro::create([
                'titulo' => $dados['titulo'],
                'id_editora' => $dados['id_editora'],
                'id_genero' => $dados['id_genero'],
            ]);

            $livro->autores()->attach($dados['autores']);

            for ($i = 0; $i < $dados['quantidade']; $i++) {
                Exemplar::create([
                    'id_livro' => $livro->id,
                    'id_conservacao' => $dados['id_conservacao'],
                    'id_funcionario' => Auth::guard('funcionario')->id(),
                    'preco_compra' => $dados['preco_compra'],
                    'preco_venda' => $dados['preco_venda'],
                    'status' => true,
                ]);
            }
        });

        return redirect()->route('livros.index')
            ->with('sucesso', "Livro cadastrado com {$dados['quantidade']} exemplar(es).");
    }

    public function update(Request $request, Exemplar $exemplar)
    {
        $dados = $request->validate([
            'titulo' => 'required|string|max:255',
            'autores' => 'required|array|min:1',
            'autores.*' => 'exists:autor,id',
            'id_editora' => 'required|exists:editora,id',
            'id_genero' => 'required|exists:genero,id',
            'id_conservacao' => 'required|exists:conservacao,id',
            'preco_compra' => 'required|numeric|min:0',
            'preco_venda' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($dados, $exemplar) {
            // Identifica TODOS os exemplares do mesmo grupo (antes de mudar os valores),
            // para aplicar a edição a todos eles de uma vez — não só ao exemplar representante.
            $idsDoGrupo = Exemplar::where('id_livro', $exemplar->id_livro)
                ->where('id_conservacao', $exemplar->id_conservacao)
                ->where('preco_compra', $exemplar->preco_compra)
                ->where('preco_venda', $exemplar->preco_venda)
                ->where('status', $exemplar->status)
                ->pluck('id');

            $exemplar->livro->update([
                'titulo' => $dados['titulo'],
                'id_editora' => $dados['id_editora'],
                'id_genero' => $dados['id_genero'],
            ]);

            $exemplar->livro->autores()->sync($dados['autores']);

            Exemplar::whereIn('id', $idsDoGrupo)->update([
                'id_conservacao' => $dados['id_conservacao'],
                'preco_compra' => $dados['preco_compra'],
                'preco_venda' => $dados['preco_venda'],
            ]);
        });

        return redirect()->route('livros.index')->with('sucesso', 'Livro atualizado com sucesso.');
    }

    public function destroy(Exemplar $exemplar)
    {
        if (!$exemplar->status) {
            return redirect()->route('livros.index')
                ->with('erro', 'Não é possível excluir exemplares já vendidos — fazem parte do histórico.');
        }

        Exemplar::where('id_livro', $exemplar->id_livro)
            ->where('id_conservacao', $exemplar->id_conservacao)
            ->where('preco_compra', $exemplar->preco_compra)
            ->where('preco_venda', $exemplar->preco_venda)
            ->where('status', true)
            ->delete();

        return redirect()->route('livros.index')->with('sucesso', 'Exemplares excluídos com sucesso.');
    }

    public function destroyAll()
    {
        Exemplar::where('status', true)->delete();

        return redirect()->route('livros.index')
            ->with('sucesso', 'Todos os exemplares disponíveis foram excluídos (vendidos não são afetados).');
    }
}
