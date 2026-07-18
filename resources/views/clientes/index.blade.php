@extends('layouts.app')

@section('titulo', 'Clientes')
@section('subtitulo', 'Gerencie os clientes do seu sebo')

@section('conteudo')

    @if (session('sucesso'))
        <div class="alert alert-success">{{ session('sucesso') }}</div>
    @endif

    @if (session('erro'))
        <div class="alert alert-danger">{{ session('erro') }}</div>
    @endif

    <div class="panel">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="mb-0">Clientes cadastrados</h3>
            </div>
            <div class="d-flex gap-2">
                <button type="button" id="btnCadastrarCliente" class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#clienteModal">
                    <i class="bi bi-plus-lg"></i> Cadastrar cliente
                </button>
                <form method="POST" action="{{ route('clientes.destroyAll') }}" class="form-confirmar d-inline" data-confirm-message="Tem certeza que deseja excluir TODOS os clientes? Essa ação não pode ser desfeita.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash"></i> Excluir todos
                    </button>
                </form>
            </div>
        </div>

        <form method="GET" action="{{ route('clientes.index') }}" class="mb-3">
            <div class="input-group" style="max-width: 360px;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" name="busca" class="form-control" placeholder="Buscar por nome ou CPF..." value="{{ $busca }}">
            </div>
        </form>

        <table class="table panel-table align-middle">
            <thead>
            <tr>
                <th>Nome</th>
                <th>CPF</th>
                <th class="text-end">Ações</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($clientes as $cliente)
                <tr>
                    <td>{{ $cliente->nome }}</td>
                    <td>{{ $cliente->cpf }}</td>
                    <td class="text-end">
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary btn-editar-cliente"
                                data-bs-toggle="modal"
                                data-bs-target="#clienteModal"
                                data-id="{{ $cliente->id }}"
                                data-nome="{{ $cliente->nome }}"
                                data-cpf="{{ $cliente->cpf }}">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <form method="POST" action="{{ route('clientes.destroy', $cliente) }}" class="form-confirmar d-inline" data-confirm-message="Excluir o cliente {{ $cliente->nome }}?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-muted text-center py-4">
                        Nenhum cliente cadastrado ainda.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <span class="text-muted small">Mostrando {{ $clientes->count() }} de {{ $clientes->total() }} registros</span>
            {{ $clientes->links() }}
        </div>

    </div>

    @include('clientes._modal-form')

@endsection

@section('scripts')
    @include('clientes._modal-scripts')
@endsection
