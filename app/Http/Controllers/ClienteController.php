<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $busca = $request->query('busca');

        $clientes = Cliente::when($busca, function ($query, $busca) {
            $query->where('nome', 'ilike', "%{$busca}%")
                ->orWhere('cpf', 'ilike', "%{$busca}%");
        })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('clientes.index', ['clientes' => $clientes, 'busca' => $busca]);
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:150',
            'cpf' => 'required|digits:11|unique:cliente,cpf',
        ], [
            'cpf.digits' => 'O CPF deve conter exatamente 11 números.',
            'cpf.unique' => 'Já existe um cliente cadastrado com esse CPF.',
        ]);

        Cliente::create($dados);

        return redirect()->route('clientes.index')->with('sucesso', 'Cliente cadastrado com sucesso.');
    }

    public function update(Request $request, Cliente $cliente)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:150',
            'cpf' => 'required|digits:11|unique:cliente,cpf,' . $cliente->id,
        ], [
            'cpf.digits' => 'O CPF deve conter exatamente 11 números.',
            'cpf.unique' => 'Já existe um cliente cadastrado com esse CPF.',
        ]);

        $cliente->update($dados);

        return redirect()->route('clientes.index')->with('sucesso', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Cliente $cliente)
    {
        try {
            $cliente->delete();
        } catch (QueryException $e) {
            return redirect()->route('clientes.index')
                ->with('erro', 'Não é possível excluir este cliente: existem vendas vinculadas a ele.');
        }

        return redirect()->route('clientes.index')->with('sucesso', 'Cliente excluído com sucesso.');
    }

    public function destroyAll()
    {
        try {
            Cliente::query()->delete();
        } catch (QueryException $e) {
            return redirect()->route('clientes.index')
                ->with('erro', 'Não foi possível excluir todos os clientes: alguns possuem vendas vinculadas.');
        }

        return redirect()->route('clientes.index')->with('sucesso', 'Todos os clientes foram excluídos.');
    }
}
