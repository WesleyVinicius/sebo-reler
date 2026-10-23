@extends('layouts.app')

@section('titulo', 'Livros')
@section('subtitulo', 'Gerencie os livros e exemplares do seu sebo')

@section('conteudo')

    @if (session('sucesso'))
        <div class="alert alert-success d-flex justify-content-between align-items-center">
            <span>{{ session('sucesso') }}</span>
            @if (session('etiquetas'))
                <a href="{{ route('livros.etiquetas', session('etiquetas')) }}" target="_blank" class="btn btn-sm btn-success">
                    <i class="bi bi-printer"></i> Imprimir etiquetas
                </a>
            @endif
        </div>
    @endif

    @if (session('erro'))
        <div class="alert alert-danger">{{ session('erro') }}</div>
    @endif

    <div class="panel">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="mb-0">Livros cadastrados</h3>
            </div>
            <div class="d-flex gap-2">
                <button type="button" id="btnCadastrarLivro" class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#livroModal">
                    <i class="bi bi-plus-lg"></i> Cadastrar livro
                </button>
                <form method="POST" action="{{ route('livros.destroyAll') }}" class="form-confirmar d-inline" data-confirm-message="Tem certeza que deseja excluir TODOS os exemplares disponíveis? Exemplares já vendidos não são afetados.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash"></i> Excluir todos
                    </button>
                </form>
            </div>
        </div>

        <form method="GET" action="{{ route('livros.index') }}" class="mb-3">
            <div class="input-group" style="max-width: 360px;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" name="busca" class="form-control" placeholder="Buscar por título..." value="{{ $busca }}">
            </div>
        </form>

        <table class="table panel-table align-middle">
            <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Autor(es)</th>
                <th>Editora</th>
                <th>Gênero</th>
                <th>Estado</th>
                <th>Preço venda</th>
                <th>Qtd.</th>
                <th>Status</th>
                <th class="text-end">Ações</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($grupos as $grupo)
                <tr>
                    <td>{{ $grupo->id }}</td>
                    <td>{{ $grupo->livro->titulo }}</td>
                    <td>{{ $grupo->livro->autores->pluck('nome')->join(', ') }}</td>
                    <td>{{ $grupo->livro->editora->nome }}</td>
                    <td>{{ $grupo->livro->genero->nome }}</td>
                    <td>{{ $grupo->conservacao->estado }}</td>
                    <td>R$ {{ number_format($grupo->preco_venda, 2, ',', '.') }}</td>
                    <td>{{ $grupo->quantidade }}</td>
                    <td>
                        @if ($grupo->status)
                            <span class="badge bg-success-subtle text-success-emphasis">Disponível</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary-emphasis">Vendido</span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($grupo->status)
                            <a href="{{ route('livros.etiquetas', $grupo) }}" class="btn btn-sm btn-outline-secondary" title="Imprimir etiquetas" target="_blank">
                                <i class="bi bi-printer"></i>
                            </a>
                        @endif
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary btn-editar-livro"
                                data-bs-toggle="modal"
                                data-bs-target="#livroModal"
                                data-id="{{ $grupo->id }}"
                                data-titulo="{{ $grupo->livro->titulo }}"
                                data-autores="{{ $grupo->livro->autores->pluck('id')->toJson() }}"
                                data-id-editora="{{ $grupo->livro->id_editora }}"
                                data-id-genero="{{ $grupo->livro->id_genero }}"
                                data-id-conservacao="{{ $grupo->id_conservacao }}"
                                data-preco-compra="{{ $grupo->preco_compra }}"
                                data-preco-venda="{{ $grupo->preco_venda }}">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <form method="POST" action="{{ route('livros.destroy', $grupo) }}" class="form-confirmar d-inline" data-confirm-message="Excluir os {{ $grupo->quantidade }} exemplar(es) de '{{ $grupo->livro->titulo }}' nesse preço/estado?">
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
                    <td colspan="10" class="text-muted text-center py-4">
                        Nenhum livro cadastrado ainda.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <span class="text-muted small">Mostrando {{ $grupos->count() }} de {{ $grupos->total() }} registros</span>
            {{ $grupos->links() }}
        </div>

    </div>

    @include('livros._modal-form')

@endsection

@section('scripts')
    @include('livros._modal-scripts')
@endsection
