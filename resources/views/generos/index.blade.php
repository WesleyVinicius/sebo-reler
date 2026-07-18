@extends('layouts.app')

@section('titulo', 'Gêneros')
@section('subtitulo', 'Gerencie os gêneros do seu sebo')

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
                <h3 class="mb-0">Gêneros cadastrados</h3>
            </div>
            <div class="d-flex gap-2">
                <button type="button" id="btnCadastrarGenero" class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#generoModal">
                    <i class="bi bi-plus-lg"></i> Cadastrar Gênero
                </button>
                <form method="POST" action="{{ route('generos.destroyAll') }}" class="form-confirmar d-inline" data-confirm-message="Tem certeza que deseja excluir TODOS os gêneros? Essa ação não pode ser desfeita.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash"></i> Excluir todos
                    </button>
                </form>
            </div>
        </div>

        <form method="GET" action="{{ route('generos.index') }}" class="mb-3">
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
            @forelse ($generos as $genero)
                <tr>
                    <td>{{ $genero->nome }}</td>
                    <td class="text-end">
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary btn-editar-genero"
                                data-bs-toggle="modal"
                                data-bs-target="#generoModal"
                                data-id="{{ $genero->id }}"
                                data-nome="{{ $genero->nome }}">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <form method="POST" action="{{ route('generos.destroy', $genero) }}" class="form-confirmar d-inline" data-confirm-message="Excluir o gênero {{ $genero->nome }}?">
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
                        Nenhum gênero cadastrado ainda.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <span class="text-muted small">Mostrando {{ $generos->count() }} de {{ $generos->total() }} registros</span>
            {{ $generos->links() }}
        </div>

    </div>

    @include('generos._modal-form')

@endsection

@section('scripts')
    @include('generos._modal-scripts')
@endsection
