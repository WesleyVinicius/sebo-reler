{{-- Modal único, reutilizado para cadastro e edição --}}
<div class="modal fade" id="conservacaoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formConservacao" method="POST" action="{{ route('conservacoes.store') }}">
                @csrf
                <div id="metodoSpoof"></div>

                <div class="modal-header">
                    <h5 class="modal-title" id="conservacaoModalTitulo">Cadastrar Estado de Conservação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Estado de conservação *</label>
                        <input type="text" name="estado" id="conservacaoEstado" class="form-control" required maxlength="30">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-accent">Salvar</button>
                </div>
            </form>

        </div>
    </div>
</div>
