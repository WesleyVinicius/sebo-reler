{{-- Modal único, reutilizado para cadastro e edição --}}
<div class="modal fade" id="livroModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formLivro" method="POST" action="{{ route('livros.store') }}">
                @csrf
                <div id="metodoSpoof"></div>

                <div class="modal-header">
                    <h5 class="modal-title" id="livroModalTitulo">Cadastrar livro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Título *</label>
                        <input type="text" name="titulo" id="livroTitulo" class="form-control" required maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Autor(es) *</label>
                        <select name="autores[]" id="livroAutores" class="form-select" multiple required size="4">
                            @foreach ($autores as $autor)
                                <option value="{{ $autor->id }}">{{ $autor->nome }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Segure Ctrl (ou Cmd no Mac) para selecionar mais de um.</small>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Editora *</label>
                            <select name="id_editora" id="livroEditora" class="form-select" required>
                                <option value="" disabled selected>Selecione</option>
                                @foreach ($editoras as $editora)
                                    <option value="{{ $editora->id }}">{{ $editora->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Gênero *</label>
                            <select name="id_genero" id="livroGenero" class="form-select" required>
                                <option value="" disabled selected>Selecione</option>
                                @foreach ($generos as $genero)
                                    <option value="{{ $genero->id }}">{{ $genero->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Estado de conservação *</label>
                        <select name="id_conservacao" id="livroConservacao" class="form-select" required>
                            <option value="" disabled selected>Selecione</option>
                            @foreach ($conservacoes as $conservacao)
                                <option value="{{ $conservacao->id }}">{{ $conservacao->estado }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Preço de compra (R$) *</label>
                            <input type="number" step="0.01" min="0" name="preco_compra" id="livroPrecoCompra" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Preço de venda (R$) *</label>
                            <input type="number" step="0.01" min="0" name="preco_venda" id="livroPrecoVenda" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3" id="livroQuantidadeGrupo">
                        <label class="form-label">Quantidade de exemplares *</label>
                        <input type="number" min="1" max="100" value="1" name="quantidade" id="livroQuantidade" class="form-control" required>
                        <small class="text-muted">Quantas cópias desse livro, com o mesmo preço e estado, serão cadastradas.</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-accent">Salvar livro</button>
                </div>
            </form>

        </div>
    </div>
</div>
