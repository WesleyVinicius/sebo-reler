{{-- Modal único, reutilizado para cadastro e edição --}}
<div class="modal fade" id="editoraModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formEditora" method="POST" action="{{ route('editoras.store') }}">
                @csrf
                <div id="metodoSpoof"></div>

                <div class="modal-header">
                    <h5 class="modal-title" id="editoraModalTitulo">Cadastrar Editora</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome *</label>
                        <input type="text" name="nome" id="editoraNome" class="form-control" required maxlength="60">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-accent">Salvar Editora</button>
                </div>
            </form>

        </div>
    </div>
</div>
