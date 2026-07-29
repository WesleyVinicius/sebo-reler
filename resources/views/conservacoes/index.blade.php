@extends('layouts.app')

@section('titulo', 'Estado de Conservação')
@section('subtitulo', 'Gerencie os estados de conservação dos livros')

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
                <h3 class="mb-0">Estados de Conservação cadastrados</h3>
            </div>
            <div class="d-flex gap-2">
                <button type="button" id="btnCadastrarConservacao" class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#conservacaoModal">
                    <i class="bi bi-plus-lg"></i> Cadastrar Estado de Conservação
                </button>
                <form method="POST" action="{{ route('conservacoes.destroyAll') }}" class="form-confirmar d-inline" data-confirm-message="Tem certeza que deseja excluir TODOS os estados de conservação? Essa ação não pode ser desfeita.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash"></i> Excluir todos
                    </button>
                </form>
            </div>
        </div>

        <table class="table panel-table align-middle">
            <thead>
            <tr>
                <th>Estado de Conservação</th>
                <th class="text-end">Ações</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($conservacoes as $conservacao)
                <tr>
                    <td>{{ $conservacao->estado }}</td>
                    <td class="text-end">
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary btn-editar-conservacao"
                                data-bs-toggle="modal"
                                data-bs-target="#conservacaoModal"
                                data-id="{{ $conservacao->id }}"
                                data-estado="{{ $conservacao->estado }}">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <form method="POST" action="{{ route('conservacoes.destroy', $conservacao) }}" class="form-confirmar d-inline" data-confirm-message="Excluir o estado '{{ $conservacao->estado }}'?">
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
                    <td colspan="3" class="text-muted text-center py-4">
                        Nenhum estado de conservação cadastrado ainda.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

    </div>

    @include('conservacoes._modal-form')

@endsection

@section('scripts')
    @include('conservacoes._modal-scripts')
@endsection
