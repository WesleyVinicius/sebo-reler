@extends('layouts.app')

@section('titulo', 'Descontos')
@section('subtitulo', 'Gerencie as faixas de desconto por valor de venda')

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
                <h3 class="mb-0">Faixas de desconto cadastradas</h3>
            </div>
            <div class="d-flex gap-2">
                <button type="button" id="btnCadastrarDesconto" class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#descontoModal">
                    <i class="bi bi-plus-lg"></i> Cadastrar desconto
                </button>
                <form method="POST" action="{{ route('descontos.destroyAll') }}" class="form-confirmar d-inline" data-confirm-message="Tem certeza que deseja excluir TODOS os descontos? Essa ação não pode ser desfeita.">
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
                <th>ID</th>
                <th>Valor mínimo</th>
                <th>Percentual</th>
                <th>Cadastrado por</th>
                <th class="text-end">Ações</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($descontos as $desconto)
                <tr>
                    <td>{{ $desconto->id }}</td>
                    <td>R$ {{ number_format($desconto->valor_minimo, 2, ',', '.') }}</td>
                    <td>{{ number_format($desconto->porcent_desconto, 2, ',', '.') }}%</td>
                    <td>{{ $desconto->funcionario?->usuario ?? '—' }}</td>
                    <td class="text-end">
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary btn-editar-desconto"
                                data-bs-toggle="modal"
                                data-bs-target="#descontoModal"
                                data-id="{{ $desconto->id }}"
                                data-valor-minimo="{{ $desconto->valor_minimo }}"
                                data-porcent-desconto="{{ $desconto->porcent_desconto }}">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <form method="POST" action="{{ route('descontos.destroy', $desconto) }}" class="form-confirmar d-inline" data-confirm-message="Excluir esta faixa de desconto?">
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
                    <td colspan="5" class="text-muted text-center py-4">
                        Nenhum desconto cadastrado ainda.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

    </div>

    @include('descontos._modal-form')

@endsection

@section('scripts')
    @include('descontos._modal-scripts')
@endsection
