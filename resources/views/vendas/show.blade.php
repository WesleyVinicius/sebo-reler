@extends('layouts.app')

@section('titulo', 'Venda #' . $venda->id)
@section('subtitulo', 'Detalhes da venda')

@section('conteudo')

    @if (session('sucesso'))
        <div class="alert alert-success">{{ session('sucesso') }}</div>
    @endif

    <div class="panel mb-3">
        <div class="row">
            <div class="col-md-4">
                <div class="text-muted small">Cliente</div>
                <div>{{ $venda->cliente->nome }} — CPF {{ $venda->cliente->cpf }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Vendedor</div>
                <div>{{ $venda->funcionario->usuario }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Data</div>
                <div>{{ $venda->data->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>

    <div class="panel mb-3">
        <h3>Itens</h3>
        <table class="table panel-table align-middle">
            <thead>
            <tr>
                <th>Título</th>
                <th>Conservação</th>
                <th class="text-end">Preço</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($venda->exemplares as $exemplar)
                <tr>
                    <td>{{ $exemplar->livro->titulo }}</td>
                    <td>{{ $exemplar->conservacao->estado }}</td>
                    <td class="text-end">R$ {{ number_format($exemplar->preco_venda, 2, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-end">
            <table class="table table-sm w-auto mb-0">
                <tr><td class="text-muted pe-4">Subtotal</td><td class="text-end">R$ {{ number_format($subtotal, 2, ',', '.') }}</td></tr>
                <tr><td class="text-muted pe-4">Desconto</td><td class="text-end text-success">- R$ {{ number_format($venda->valor_desconto, 2, ',', '.') }}</td></tr>
                <tr class="fw-bold"><td class="pe-4">Total</td><td class="text-end">R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</td></tr>
            </table>
        </div>
    </div>

    <a href="{{ route('vendas.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>

@endsection
