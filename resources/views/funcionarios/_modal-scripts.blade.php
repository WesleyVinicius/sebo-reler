<script>
    document.getElementById('btnCadastrarFuncionario').addEventListener('click', function () {
        document.getElementById('funcionarioModalTitulo').innerText = 'Cadastrar funcionário';
        document.getElementById('formFuncionario').action = "{{ route('funcionarios.store') }}";
        document.getElementById('metodoSpoof').innerHTML = '';
        document.getElementById('funcionarioIdHidden').value = '';

        document.getElementById('funcionarioUsuario').value = '';
        document.getElementById('funcionarioCpf').value = '';
        document.getElementById('funcionarioRg').value = '';
        document.getElementById('funcionarioSexo').value = '';
        document.getElementById('funcionarioDataNascimento').value = '';
        document.getElementById('funcionarioSalario').value = '';
        document.getElementById('funcionarioPerfil').value = '';

        // No cadastro, senha é obrigatória
        document.getElementById('funcionarioSenha').value = '';
        document.getElementById('funcionarioSenha').required = true;
        document.getElementById('funcionarioSenhaLabel').innerText = 'Senha *';
        document.getElementById('funcionarioSenhaAjuda').style.display = 'none';
    });

    document.querySelectorAll('.btn-editar-funcionario').forEach(function (botao) {
        botao.addEventListener('click', function () {
            document.getElementById('funcionarioModalTitulo').innerText = 'Editar funcionário';
            document.getElementById('formFuncionario').action = '/funcionarios/' + botao.dataset.id;
            document.getElementById('metodoSpoof').innerHTML = '@method('PUT')';
            document.getElementById('funcionarioIdHidden').value = botao.dataset.id;

            document.getElementById('funcionarioUsuario').value = botao.dataset.usuario;
            document.getElementById('funcionarioCpf').value = botao.dataset.cpf;
            document.getElementById('funcionarioRg').value = botao.dataset.rg;
            document.getElementById('funcionarioSexo').value = botao.dataset.sexo;
            document.getElementById('funcionarioDataNascimento').value = botao.dataset.dataNascimento;
            document.getElementById('funcionarioSalario').value = botao.dataset.salario;
            document.getElementById('funcionarioPerfil').value = botao.dataset.perfil;

            // Na edição, senha é opcional — deixa em branco por segurança (nunca mostra a atual)
            document.getElementById('funcionarioSenha').value = '';
            document.getElementById('funcionarioSenha').required = false;
            document.getElementById('funcionarioSenhaLabel').innerText = 'Senha';
            document.getElementById('funcionarioSenhaAjuda').style.display = 'block';
        });
    });

    @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                var modal = new bootstrap.Modal(document.getElementById('funcionarioModal'));

                @if (old('_method') === 'PUT')
                document.getElementById('funcionarioModalTitulo').innerText = 'Editar funcionário';
                document.getElementById('formFuncionario').action = '/funcionarios/{{ old('funcionario_id') }}';
                document.getElementById('metodoSpoof').innerHTML = '@method('PUT')';
                document.getElementById('funcionarioSenha').required = false;
                document.getElementById('funcionarioSenhaAjuda').style.display = 'block';
                @else
                document.getElementById('funcionarioModalTitulo').innerText = 'Cadastrar funcionário';
                document.getElementById('formFuncionario').action = "{{ route('funcionarios.store') }}";
                @endif

                modal.show();
            });
   @endif
</script>
