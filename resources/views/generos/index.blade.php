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
                <button type="button" id="btnCadastrarGenero" class="btn btn-reler" data-bs-toggle="modal" data-bs-target="#generoModal">
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

    {{-- Modal único, reutilizado para cadastro e edição --}}
    <div class="modal fade" id="generoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="formGenero" method="POST" action="{{ route('generos.store') }}">
                    @csrf
                    <div id="metodoSpoof"></div>

                    <div class="modal-header">
                        <h5 class="modal-title" id="generoModalTitulo">Cadastrar Gênero</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nome *</label>
                            <input type="text" name="nome" id="generoNome" class="form-control" required maxlength="60">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-reler">Salvar Gênero</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        document.getElementById('btnCadastrarGenero').addEventListener('click', function () {
            document.getElementById('generoModalTitulo').innerText = 'Cadastrar genero';
            document.getElementById('formGenero').action = "{{ route('generos.store') }}";
            document.getElementById('metodoSpoof').innerHTML = '';
            document.getElementById('generoNome').value = '';
        });

        document.querySelectorAll('.btn-editar-genero').forEach(function (botao) {
            botao.addEventListener('click', function () {
                document.getElementById('generoModalTitulo').innerText = 'Editar genero';
                document.getElementById('formGenero').action = '/generos/' + botao.dataset.id;
                document.getElementById('metodoSpoof').innerHTML = '@method('PUT')';
                document.getElementById('generoNome').value = botao.dataset.nome;
            });
        });
    </script>
@endsection
