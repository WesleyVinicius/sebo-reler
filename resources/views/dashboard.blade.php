@extends('layouts.app')

@section('titulo', 'Dashboard')
@section('subtitulo', 'Visão geral do sistema')

@section('conteudo')

    <div class="welcome-banner">
        <h2>Bem-vindo de volta, {{ $funcionario->usuario }}!</h2>
        <p>Aqui está um resumo do que acontece no seu sebo hoje.</p>
    </div>

    {{-- ⚠️ Números mockados por enquanto — trocar por dados reais (Livro::count(), etc)
         quando os CRUDs e a lógica de vendas estiverem prontos. --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-book"></i></div>
            <div>
                <div class="stat-label">Livros cadastrados</div>
                <div class="stat-value">0</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-pen"></i></div>
            <div>
                <div class="stat-label">Autores cadastrados</div>
                <div class="stat-value">0</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-tag"></i></div>
            <div>
                <div class="stat-label">Vendas (mês)</div>
                <div class="stat-value">0</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-cart"></i></div>
            <div>
                <div class="stat-label">Faturamento (mês)</div>
                <div class="stat-value">R$ 0,00</div>
            </div>
        </div>
    </div>

    <div class="panels-grid">
        <div class="panel">
            <div class="panel-header">
                <h3>Vendas recentes</h3>
                <a href="#" class="panel-link">Ver todas</a>
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
                <tr>
                    <td colspan="4" class="text-muted text-center py-4">
                        Nenhuma venda registrada ainda.
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h3>Livros adicionados recentemente</h3>
                <a href="#" class="panel-link">Ver todos</a>
            </div>
            <div class="text-muted text-center py-4">
                Nenhum livro cadastrado ainda.
            </div>
        </div>
    </div>

@endsection
