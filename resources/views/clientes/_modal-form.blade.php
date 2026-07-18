{{-- Modal único, reutilizado para cadastro e edição --}}
<div class="modal fade" id="clienteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formCliente" method="POST" action="{{ route('clientes.store') }}">
                @csrf
                <div id="metodoSpoof"></div>

                <div class="modal-header">
                    <h5 class="modal-title" id="clienteModalTitulo">Cadastrar cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome *</label>
                        <input type="text" name="nome" id="clienteNome" class="form-control" required maxlength="150">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">CPF *</label>
                        <input type="text" name="cpf" id="clienteCpf" class="form-control" required maxlength="11" placeholder="Somente números">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-reler">Salvar cliente</button>
                </div>
            </form>

        </div>
    </div>
</div>
