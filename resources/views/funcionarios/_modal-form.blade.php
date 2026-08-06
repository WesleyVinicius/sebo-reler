{{-- Modal único, reutilizado para cadastro e edição --}}
<div class="modal fade" id="funcionarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formFuncionario" method="POST" action="{{ route('funcionarios.store') }}">
                @csrf
                <div id="metodoSpoof"></div>
                <input type="hidden" name="funcionario_id" id="funcionarioIdHidden">

                <div class="modal-header">
                    <h5 class="modal-title" id="funcionarioModalTitulo">Cadastrar Funcionário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Usuário *</label>
                        <input type="text" name="usuario" id="funcionarioUsuario" value="{{ old('usuario') }}"
                               class="form-control @error('usuario') is-invalid @enderror" required maxlength="50">
                        @error('usuario')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" id="funcionarioSenhaLabel">Senha *</label>
                        <input type="password" name="senha" id="funcionarioSenha" class="form-control" placeholder="Mínimo 6 caracteres">
                        <small class="text-muted" id="funcionarioSenhaAjuda" style="display: none;">
                            Deixe em branco para manter a senha atual.
                        </small>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">CPF *</label>
                            <input type="text" name="cpf" id="funcionarioCpf" value="{{ old('cpf') }}"
                                   class="form-control @error('cpf') is-invalid @enderror" required maxlength="11" placeholder="Somente números">
                            @error('cpf')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">RG *</label>
                            <input type="text" name="rg" id="funcionarioRg" class="form-control" required maxlength="20">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Sexo *</label>
                            <select name="sexo" id="funcionarioSexo" class="form-select" required>
                                <option value="" disabled selected>Selecione</option>
                                @foreach ($opcoesSexo as $opcao)
                                    <option value="{{ $opcao }}">{{ $opcao }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Data de nascimento *</label>
                            <input type="date" name="data_nascimento" id="funcionarioDataNascimento" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Salário (R$) *</label>
                            <input type="number" step="0.01" min="0" name="salario" id="funcionarioSalario" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Perfil *</label>
                            <select name="perfil" id="funcionarioPerfil" class="form-select" required>
                                <option value="" disabled selected>Selecione</option>
                                @foreach ($opcoesPerfil as $opcao)
                                    <option value="{{ $opcao }}">{{ $opcao }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-accent">Salvar Funcionário</button>
                </div>
            </form>

        </div>
    </div>
</div>
