@extends('layouts.app')

@section('titulo', 'Editoras')
@section('subtitulo', 'Gerencie as editoras do seu sebo')

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
                <h3 class="mb-0">Editoras cadastradas</h3>
            </div>
            <div class="d-flex gap-2">
                <button type="button" id="btnCadastrarEditora" class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#editoraModal">
                    <i class="bi bi-plus-lg"></i> Cadastrar Editora
                </button>
                <form method="POST" action="{{ route('editoras.destroyAll') }}" class="form-confirmar d-inline" data-confirm-message="Tem certeza que deseja excluir TODAS as editoras? Essa ação não pode ser desfeita.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash"></i> Excluir todos
                    </button>
                </form>
            </div>
        </div>

        <form method="GET" action="{{ route('editoras.index') }}" class="mb-3">
            <div class="input-group" style="max-width: 360px;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" name="busca" class="form-control" placeholder="Buscar por nome..." value="{{ $busca }}">
            </div>
        </form>

        <table class="table panel-table align-middle">
            <thead>
            <tr>
                <th>Nome</th>
                <th class="text-end">Ações</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($editoras as $editora)
                <tr>
                    <td>{{ $editora->nome }}</td>
                    <td class="text-end">
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary btn-editar-editora"
                                data-bs-toggle="modal"
                                data-bs-target="#editoraModal"
                                data-id="{{ $editora->id }}"
                                data-nome="{{ $editora->nome }}">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <form method="POST" action="{{ route('editoras.destroy', $editora) }}" class="form-confirmar d-inline" data-confirm-message="Excluir a editora {{ $editora->nome }}?">
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
                        Nenhuma editora cadastrada ainda.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <span class="text-muted small">Mostrando {{ $editoras->count() }} de {{ $editoras->total() }} registros</span>
            {{ $editoras->links() }}
        </div>

    </div>

    @include('editoras._modal-form')

@endsection

@section('scripts')
    @include('editoras._modal-scripts')
@endsection
