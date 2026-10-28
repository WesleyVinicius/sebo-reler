@extends('layouts.app')

@section('titulo', 'Vendas')
@section('subtitulo', 'Histórico de vendas realizadas')

@section('conteudo')

    @if (session('sucesso'))
        <div class="alert alert-success">{{ session('sucesso') }}</div>
    @endif

    <div class="panel">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Vendas realizadas</h3>
            <a href="{{ route('vendas.create') }}" class="btn btn-accent">
                <i class="bi bi-plus-lg"></i> Nova venda
            </a>
        </div>

        <table class="table panel-table align-middle">
            <thead>
            <tr>
                <th>Nº</th>
                <th>Data</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th class="text-center">Itens</th>
                <th class="text-end">Desconto</th>
                <th class="text-end">Total</th>
                <th class="text-end">Ações</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($vendas as $venda)
                <tr>
                    <td>{{ $venda->id }}</td>
                    <td>{{ $venda->data->format('d/m/Y H:i') }}</td>
                    <td>{{ $venda->cliente->nome }}</td>
                    <td>{{ $venda->funcionario->usuario }}</td>
                    <td class="text-center">{{ $venda->exemplares_count }}</td>
                    <td class="text-end">R$ {{ number_format($venda->valor_desconto, 2, ',', '.') }}</td>
                    <td class="text-end">R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</td>
                    <td class="text-end">
                        <a href="{{ route('vendas.show', $venda) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-muted text-center py-4">
                        Nenhuma venda registrada ainda.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $vendas->links() }}

    </div>

@endsection
