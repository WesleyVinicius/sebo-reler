@extends('layouts.app')

@section('titulo', 'Funcionários')
@section('subtitulo', 'Gerencie os funcionários do seu sebo')

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
                <h3 class="mb-0">Funcionários cadastrados</h3>
            </div>
            <div class="d-flex gap-2">
                <button type="button" id="btnCadastrarFuncionario" class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#funcionarioModal">
                    <i class="bi bi-plus-lg"></i> Cadastrar funcionário
                </button>
                <form method="POST" action="{{ route('funcionarios.destroyAll') }}" class="form-confirmar d-inline" data-confirm-message="Tem certeza que deseja excluir TODOS os funcionários? Essa ação não pode ser desfeita.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash"></i> Excluir todos
                    </button>
                </form>
            </div>
        </div>

        <form method="GET" action="{{ route('funcionarios.index') }}" class="mb-3">
            <div class="input-group" style="max-width: 360px;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" name="busca" class="form-control" placeholder="Buscar por usuário ou CPF..." value="{{ $busca }}">
            </div>
        </form>

        <table class="table panel-table align-middle">
            <thead>
            <tr>
                <th>Usuário</th>
                <th>CPF</th>
                <th>Perfil</th>
                <th>Salário</th>
                <th class="text-end">Ações</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($funcionarios as $funcionario)
                <tr>
                    <td>{{ $funcionario->usuario }}</td>
                    <td>{{ $funcionario->cpf }}</td>
                    <td><span class="user-badge">{{ $funcionario->perfil }}</span></td>
                    <td>R$ {{ number_format($funcionario->salario, 2, ',', '.') }}</td>
                    <td class="text-end">
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary btn-editar-funcionario"
                                data-bs-toggle="modal"
                                data-bs-target="#funcionarioModal"
                                data-id="{{ $funcionario->id }}"
                                data-usuario="{{ $funcionario->usuario }}"
                                data-cpf="{{ $funcionario->cpf }}"
                                data-rg="{{ $funcionario->rg }}"
                                data-sexo="{{ $funcionario->sexo }}"
                                data-data-nascimento="{{ $funcionario->data_nascimento }}"
                                data-salario="{{ $funcionario->salario }}"
                                data-perfil="{{ $funcionario->perfil }}">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <form method="POST" action="{{ route('funcionarios.destroy', $funcionario) }}" class="form-confirmar d-inline" data-confirm-message="Excluir o funcionário {{ $funcionario->usuario }}?">
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
                    <td colspan="6" class="text-muted text-center py-4">
                        Nenhum funcionário cadastrado ainda.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <span class="text-muted small">Mostrando {{ $funcionarios->count() }} de {{ $funcionarios->total() }} registros</span>
            {{ $funcionarios->links() }}
        </div>

    </div>

    @include('funcionarios._modal-form')

@endsection

@section('scripts')
    @include('funcionarios._modal-scripts')
@endsection
