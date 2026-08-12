{{-- Modal único, reutilizado para cadastro e edição --}}
<div class="modal fade" id="descontoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formDesconto" method="POST" action="{{ route('descontos.store') }}">
                @csrf
                <div id="metodoSpoof"></div>

                <div class="modal-header">
                    <h5 class="modal-title" id="descontoModalTitulo">Cadastrar desconto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Valor mínimo da venda (R$) *</label>
                        <input type="number" step="0.01" min="0" name="valor_minimo" id="descontoValorMinimo" class="form-control" required>
                        <small class="text-muted">A partir de qual valor de venda esse desconto se aplica.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Percentual de desconto (%) *</label>
                        <input type="number" step="0.01" min="0" max="100" name="porcent_desconto" id="descontoPorcentual" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-accent">Salvar desconto</button>
                </div>
            </form>

        </div>
    </div>
</div>
