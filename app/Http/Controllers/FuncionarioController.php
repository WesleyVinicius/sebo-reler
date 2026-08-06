<?php

namespace App\Http\Controllers;

use App\Models\Funcionario;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

class FuncionarioController extends Controller
{
    public const OPCOES_PERFIL = ['GERENTE', 'VENDEDOR'];
    public const OPCOES_SEXO = ['Feminino', 'Masculino', 'Outro'];

    public function index(Request $request)
    {
        $busca = $request->query('busca');

        $funcionarios = Funcionario::when($busca, function ($query, $busca) {
            $query->where('usuario', 'ilike', "%{$busca}%")
                ->orWhere('cpf', 'ilike', "%{$busca}%");
        })
            ->orderBy('usuario')
            ->paginate(10)
            ->withQueryString();

        return view('funcionarios.index', [
            'funcionarios' => $funcionarios,
            'busca' => $busca,
            'opcoesPerfil' => self::OPCOES_PERFIL,
            'opcoesSexo' => self::OPCOES_SEXO,
        ]);
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'usuario' => 'required|string|max:50|unique:funcionario,usuario',
            'senha' => 'required|string|min:6',
            'cpf' => 'required|digits:11|unique:funcionario,cpf',
            'rg' => 'required|string|max:20',
            'sexo' => 'required|in:' . implode(',', self::OPCOES_SEXO),
            'data_nascimento' => 'required|date',
            'salario' => 'required|numeric|min:0',
            'perfil' => 'required|in:' . implode(',', self::OPCOES_PERFIL),
        ], [
            'usuario.unique' => 'Esse nome de usuário já está em uso.',
            'cpf.unique' => 'Já existe um funcionário cadastrado com esse CPF.',
            'senha.min' => 'A senha deve ter pelo menos 6 caracteres.',
        ]);

        $dados['senha'] = Hash::make($dados['senha']);

        Funcionario::create($dados);

        $mensagem = 'Funcionário cadastrado com sucesso.';

        if ($request->wantsJson()) {
            session()->flash('sucesso', $mensagem);
            return response()->json(['sucesso' => true]);
        }

        return redirect()->route('funcionarios.index')->with('sucesso', $mensagem);
    }

    public function update(Request $request, Funcionario $funcionario)
    {
        $dados = $request->validate([
            'usuario' => 'required|string|max:50|unique:funcionario,usuario,' . $funcionario->id,
            'senha' => 'nullable|string|min:6',
            'cpf' => 'required|digits:11|unique:funcionario,cpf,' . $funcionario->id,
            'rg' => 'required|string|max:20',
            'sexo' => 'required|in:' . implode(',', self::OPCOES_SEXO),
            'data_nascimento' => 'required|date',
            'salario' => 'required|numeric|min:0',
            'perfil' => 'required|in:' . implode(',', self::OPCOES_PERFIL),
        ], [
            'usuario.unique' => 'Esse nome de usuário já está em uso.',
            'cpf.unique' => 'Já existe um funcionário cadastrado com esse CPF.',
            'senha.min' => 'A senha deve ter pelo menos 6 caracteres.',
        ]);

        // Senha só entra na atualização se o campo foi preenchido;
        // se veio em branco, remove do array para não sobrescrever a senha atual.
        if (!empty($dados['senha'])) {
            $dados['senha'] = Hash::make($dados['senha']);
        } else {
            unset($dados['senha']);
        }

        $funcionario->update($dados);

        $mensagem = 'Funcionário atualizado com sucesso.';

        if ($request->wantsJson()) {
            session()->flash('sucesso', $mensagem);
            return response()->json(['sucesso' => true]);
        }

        return redirect()->route('funcionarios.index')->with('sucesso', $mensagem);
    }

    public function destroy(Funcionario $funcionario)
    {
        try {
            $funcionario->delete();
        } catch (QueryException $e) {
            return redirect()->route('funcionarios.index')
                ->with('erro', 'Não é possível excluir, existem vendas, descontos ou exemplares vinculados a este funcionário.');
        }

        return redirect()->route('funcionarios.index')->with('sucesso', 'Funcionário excluído com sucesso.');
    }

    public function destroyAll()
    {
        try {
            Funcionario::query()->delete();
        } catch (QueryException $e) {
            return redirect()->route('funcionarios.index')
                ->with('erro', 'Não foi possível excluir todos os funcionários, alguns possuem vínculos com vendas, descontos ou exemplares.');
        }

        return redirect()->route('funcionarios.index')->with('sucesso', 'Todos os funcionários foram excluídos.');
    }
}
