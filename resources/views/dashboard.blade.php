@extends('layouts.app')

@section('titulo', 'Dashboard')
@section('subtitulo', 'Visão geral do sistema')

@section('conteudo')

    <div class="welcome-banner">
        <h2>Bem-vindo de volta, {{ $funcionario->usuario }}!</h2>
        <p>Aqui está um resumo do que acontece no seu sebo hoje.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-book"></i></div>
            <div>
                <div class="stat-label">Exemplares disponíveis</div>
                <div class="stat-value">{{ $exemplaresDisponiveis }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-people"></i></div>
            <div>
                <div class="stat-label">Clientes cadastrados</div>
                <div class="stat-value">{{ $totalClientes }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-tag"></i></div>
            <div>
                <div class="stat-label">Vendas (mês)</div>
                <div class="stat-value">{{ $vendasMes }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-cart"></i></div>
            <div>
                <div class="stat-label">Faturamento (mês)</div>
                <div class="stat-value">R$ {{ number_format($faturamentoMes, 2, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="panels-grid">
        <div class="panel">
            <div class="panel-header">
                <h3>Vendas recentes</h3>
                <a href="{{ route('vendas.index') }}" class="panel-link">Ver todas</a>
            </div>
            <table class="table panel-table">
                <thead>
                <tr>
                    <th>Data</th>
                    <th>Cliente</th>
                    <th>Itens</th>
                    <th class="text-end">Total</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($vendasRecentes as $venda)
                    <tr>
                        <td>{{ $venda->data->format('d/m/Y H:i') }}</td>
                        <td>{{ $venda->cliente->nome }}</td>
                        <td>{{ $venda->exemplares_count }}</td>
                        <td class="text-end">R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-muted text-center py-4">
                            Nenhuma venda registrada ainda.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h3>Livros adicionados recentemente</h3>
                <a href="{{ route('livros.index') }}" class="panel-link">Ver todos</a>
            </div>
            @forelse ($livrosRecentes as $livro)
                <div class="py-2 border-bottom">
                    <div class="fw-semibold">{{ $livro->titulo }}</div>
                    <div class="text-muted small">
                        {{ $livro->autores->pluck('nome')->join(', ') }}
                        · {{ $livro->created_at->format('d/m/Y') }}
                    </div>
                </div>
            @empty
                <div class="text-muted text-center py-4">
                    Nenhum livro cadastrado ainda.
                </div>
            @endforelse
        </div>
    </div>

@endsection
