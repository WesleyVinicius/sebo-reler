<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiquetas - Reler</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { background: #f3f0e9; }

        .grade {
            display: grid;
            grid-template-columns: repeat(3, 64mm);
            gap: 4mm;
            justify-content: center;
        }

        .etiqueta {
            width: 64mm;
            height: 38mm;
            padding: 2.5mm;
            background: #fff;
            border: 1px dashed #999;      /* guia de corte */
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            break-inside: avoid;
        }

        .etiqueta .loja   { font-size: 7pt; letter-spacing: .1em; text-transform: uppercase; color: #555; }
        .etiqueta .titulo { font-size: 9pt; font-weight: 700; line-height: 1.15; max-height: 2.3em; overflow: hidden; }
        .etiqueta .autor  { font-size: 7.5pt; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .etiqueta .linha  { display: flex; justify-content: space-between; align-items: baseline; font-size: 8pt; }
        .etiqueta .preco  { font-size: 11pt; font-weight: 700; }
        .etiqueta svg     { width: 100%; height: 12mm; }

        @media print {
            @page { size: A4; margin: 10mm; }
            body { background: #fff; }
            .no-print { display: none !important; }
            .etiqueta { border: 1px dashed #bbb; }
        }
    </style>
</head>
<body>

<div class="container py-3 no-print">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-0">Etiquetas para impressão</h4>
            <div class="text-muted small">{{ $exemplares->count() }} etiqueta(s) — use Ctrl+P e desative "Cabeçalhos e rodapés" na impressão</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('livros.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
            <button type="button" class="btn btn-accent" onclick="window.print()">
                <i class="bi bi-printer"></i> Imprimir
            </button>
        </div>
    </div>
</div>

@if ($exemplares->isEmpty())
    <div class="container no-print">
        <div class="alert alert-warning">Não há exemplares disponíveis neste grupo para etiquetar.</div>
    </div>
@else
    <div class="grade">
        @foreach ($exemplares as $exemplar)
            <div class="etiqueta">
                <div class="loja">Reler · Sebo</div>
                <div>
                    <div class="titulo">{{ $exemplar->livro->titulo }}</div>
                    <div class="autor">{{ $exemplar->livro->autores->pluck('nome')->join(', ') }}</div>
                </div>
                <div class="linha">
                    <span>{{ $exemplar->conservacao->estado }}</span>
                    <span class="preco">R$ {{ number_format($exemplar->preco_venda, 2, ',', '.') }}</span>
                </div>
                <svg class="barcode" data-codigo="{{ $exemplar->id }}"></svg>
            </div>
        @endforeach
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.barcode').forEach(function (svg) {
            JsBarcode(svg, svg.dataset.codigo, {
                format: 'CODE128',
                displayValue: true,
                fontSize: 12,
                height: 40,
                width: 1.6,
                margin: 0
            });
        });
    });
</script>

</body>
</html>
