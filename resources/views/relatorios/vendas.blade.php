@extends('layouts.app')

@section('titulo', 'Relatório de vendas')
@section('subtitulo', 'Consulte as vendas por período')

@section('conteudo')

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $erro)
                <div>{{ $erro }}</div>
            @endforeach
        </div>
    @endif

    {{-- FILTRO --}}
    <div class="panel mb-3">
        <form method="GET" action="{{ route('relatorios.vendas') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="data_inicio" class="form-label">Data inicial</label>
                <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="{{ $inicio }}">
            </div>
            <div class="col-md-4">
                <label for="data_fim" class="form-label">Data final</label>
                <input type="date" name="data_fim" id="data_fim" class="form-control" value="{{ $fim }}">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-accent">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                <a href="{{ route('relatorios.vendas') }}" class="btn btn-outline-secondary">Mês atual</a>
            </div>
            <a href="{{ route('relatorios.vendas.imprimir', ['data_inicio' => $inicio, 'data_fim' => $fim]) }}"
               target="_blank" class="btn btn-outline-secondary ms-auto">
                <i class="bi bi-printer"></i> Imprimir / PDF
            </a>
        </form>
    </div>

    {{-- RESUMO --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg">
            <div class="panel h-100">
                <div class="text-muted small">Vendas</div>
                <div class="fs-3 fw-semibold">{{ $resumo['quantidade'] }}</div>
            </div>
        </div>
        <div class="col-6 col-lg">
            <div class="panel h-100">
                <div class="text-muted small">Livros vendidos</div>
                <div class="fs-3 fw-semibold">{{ $resumo['itens'] }}</div>
            </div>
        </div>
        <div class="col-6 col-lg">
            <div class="panel h-100">
                <div class="text-muted small">Valor bruto</div>
                <div class="fs-3 fw-semibold">R$ {{ number_format($resumo['bruto'], 2, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-6 col-lg">
            <div class="panel h-100">
                <div class="text-muted small">Descontos</div>
                <div class="fs-3 fw-semibold text-success">- R$ {{ number_format($resumo['descontos'], 2, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-12 col-lg">
            <div class="panel h-100">
                <div class="text-muted small">Total líquido</div>
                <div class="fs-3 fw-semibold">R$ {{ number_format($resumo['liquido'], 2, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3">

        {{-- POR VENDEDOR --}}
        <div class="col-lg-4">
            <div class="panel h-100">
                <h3>Por vendedor</h3>
                <table class="table panel-table align-middle">
                    <thead>
                    <tr>
                        <th>Funcionário</th>
                        <th class="text-center">Vendas</th>
                        <th class="text-end">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($porVendedor as $linha)
                        <tr>
                            <td>{{ $linha->funcionario->nome }}</td>
                            <td class="text-center">{{ $linha->quantidade }}</td>
                            <td class="text-end">R$ {{ number_format($linha->total, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-muted text-center py-4">Sem vendas no período.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- LISTA DE VENDAS --}}
        <div class="col-lg-8">
            <div class="panel h-100">
                <h3>Vendas do período</h3>
                <table class="table panel-table align-middle">
                    <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th class="text-center">Itens</th>
                        <th class="text-end">Total</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($vendas as $venda)
                        <tr>
                            <td>{{ $venda->id }}</td>
                            <td>{{ $venda->data->format('d/m/Y H:i') }}</td>
                            <td>{{ $venda->cliente->nome }}</td>
                            <td>{{ $venda->funcionario->nome }}</td>
                            <td class="text-center">{{ $venda->exemplares_count }}</td>
                            <td class="text-end">R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</td>
                            <td class="text-end">
                                <a href="{{ route('vendas.show', $venda) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-muted text-center py-4">
                                Nenhuma venda encontrada neste período.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                {{ $vendas->links() }}
            </div>
        </div>

    </div>

@endsection
