<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de vendas - Reler</title>

    @vite(['resources/css/app.css'])

    <style>
        body { background: #f3f0e9; }

        .folha {
            max-width: 190mm;
            margin: 0 auto 2rem;
            padding: 12mm;
            background: #fff;
            color: #000;
        }

        .folha h1 { font-size: 16pt; margin: 0; }
        .folha h2 { font-size: 11pt; margin: 1.2rem 0 .4rem; border-bottom: 1px solid #000; padding-bottom: 2px; }
        .folha table { width: 100%; font-size: 9pt; border-collapse: collapse; }
        .folha th, .folha td { padding: 3px 4px; border-bottom: 1px solid #ccc; }
        .folha th { text-align: left; border-bottom: 1px solid #000; }
        .folha .dir { text-align: right; }
        .folha .centro { text-align: center; }
        .folha tr { break-inside: avoid; }
        .folha tfoot td { font-weight: 700; border-top: 1px solid #000; border-bottom: 0; }
        .cabecalho { display: flex; justify-content: space-between; align-items: center; }
        .cabecalho img { height: 14mm; }
        .rodape { margin-top: 1rem; font-size: 8pt; color: #555; }

        @media print {
            @page { size: A4; margin: 10mm; }
            body { background: #fff; }
            .no-print { display: none !important; }
            .folha { max-width: none; margin: 0; padding: 0; }
        }
    </style>
</head>
<body>

<div class="container py-3 no-print">
    <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted small">Use Ctrl+P e escolha "Salvar como PDF" para gerar o arquivo.</div>
        <button type="button" class="btn btn-accent" onclick="window.print()">
            <i class="bi bi-printer"></i> Imprimir / Salvar PDF
        </button>
    </div>
</div>

<div class="folha">

    <div class="cabecalho">
        <div>
            <h1>Relatório de vendas</h1>
            <div>Período: {{ $inicio->format('d/m/Y') }} a {{ $fim->format('d/m/Y') }}</div>
        </div>
        <img src="{{ asset('images/logo_reler.png') }}" alt="Reler">
    </div>

    <h2>Resumo</h2>
    <table>
        <tr><td>Vendas realizadas</td><td class="dir">{{ $resumo['quantidade'] }}</td></tr>
        <tr><td>Livros vendidos</td><td class="dir">{{ $resumo['itens'] }}</td></tr>
        <tr><td>Valor bruto</td><td class="dir">R$ {{ number_format($resumo['bruto'], 2, ',', '.') }}</td></tr>
        <tr><td>Descontos concedidos</td><td class="dir">- R$ {{ number_format($resumo['descontos'], 2, ',', '.') }}</td></tr>
        <tr><td><strong>Total líquido</strong></td><td class="dir"><strong>R$ {{ number_format($resumo['liquido'], 2, ',', '.') }}</strong></td></tr>
    </table>

    <h2>Por vendedor</h2>
    <table>
        <thead>
        <tr>
            <th>Funcionário</th>
            <th class="centro">Vendas</th>
            <th class="dir">Total</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($porVendedor as $linha)
            <tr>
                <td>{{ $linha->funcionario->nome }}</td>
                <td class="centro">{{ $linha->quantidade }}</td>
                <td class="dir">R$ {{ number_format($linha->total, 2, ',', '.') }}</td>
            </tr>
        @empty
            <tr><td colspan="3">Sem vendas no período.</td></tr>
        @endforelse
        </tbody>
    </table>

    <h2>Vendas do período</h2>
    <table>
        <thead>
        <tr>
            <th>Nº</th>
            <th>Data</th>
            <th>Cliente</th>
            <th>Vendedor</th>
            <th class="centro">Itens</th>
            <th class="dir">Desconto</th>
            <th class="dir">Total</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($vendas as $venda)
            <tr>
                <td>{{ $venda->id }}</td>
                <td>{{ $venda->data->format('d/m/Y H:i') }}</td>
                <td>{{ $venda->cliente->nome }}</td>
                <td>{{ $venda->funcionario->nome }}</td>
                <td class="centro">{{ $venda->exemplares_count }}</td>
                <td class="dir">R$ {{ number_format($venda->valor_desconto, 2, ',', '.') }}</td>
                <td class="dir">R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</td>
            </tr>
        @empty
            <tr><td colspan="7">Nenhuma venda encontrada neste período.</td></tr>
        @endforelse
        </tbody>
        @if ($vendas->isNotEmpty())
            <tfoot>
            <tr>
                <td colspan="4">Total</td>
                <td class="centro">{{ $resumo['itens'] }}</td>
                <td class="dir">R$ {{ number_format($resumo['descontos'], 2, ',', '.') }}</td>
                <td class="dir">R$ {{ number_format($resumo['liquido'], 2, ',', '.') }}</td>
            </tr>
            </tfoot>
        @endif
    </table>

    <div class="rodape">
        Gerado por {{ $geradoPor }} em {{ $geradoEm->format('d/m/Y H:i') }}
    </div>
</div>

</body>
</html>
