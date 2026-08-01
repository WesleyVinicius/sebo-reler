@extends('layouts.app')

@section('titulo', 'Autores')
@section('subtitulo', 'Gerencie os autores do seu sebo')

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
                <h3 class="mb-0">Autores cadastrados</h3>
            </div>
            <div class="d-flex gap-2">
                <button type="button" id="btnCadastrarAutor" class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#autorModal">
                    <i class="bi bi-plus-lg"></i> Cadastrar Autor
                </button>
                <form method="POST" action="{{ route('autores.destroyAll') }}" class="form-confirmar d-inline" data-confirm-message="Tem certeza que deseja excluir TODOS os autores? Essa ação não pode ser desfeita.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash"></i> Excluir todos
                    </button>
                </form>
            </div>
        </div>

        <form method="GET" action="{{ route('autores.index') }}" class="mb-3">
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
            @forelse ($autores as $autor)
                <tr>
                    <td>{{ $autor->nome }}</td>
                    <td class="text-end">
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary btn-editar-autor"
                                data-bs-toggle="modal"
                                data-bs-target="#autorModal"
                                data-id="{{ $autor->id }}"
                                data-nome="{{ $autor->nome }}">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <form method="POST" action="{{ route('autores.destroy', $autor) }}" class="form-confirmar d-inline" data-confirm-message="Excluir o autor {{ $autor->nome }}?">
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
                        Nenhum autor cadastrado ainda.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <span class="text-muted small">Mostrando {{ $autores->count() }} de {{ $autores->total() }} registros</span>
            {{ $autores->links() }}
        </div>

    </div>

    @include('autores._modal-form')

@endsection

@section('scripts')
    @include('autores._modal-scripts')
@endsection
