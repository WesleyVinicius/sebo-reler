<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mapaCamposFuncionario = {
            usuario: 'funcionarioUsuario',
            senha: 'funcionarioSenha',
            cpf: 'funcionarioCpf',
            rg: 'funcionarioRg',
            sexo: 'funcionarioSexo',
            data_nascimento: 'funcionarioDataNascimento',
            salario: 'funcionarioSalario',
            perfil: 'funcionarioPerfil',
        };

        function limparErrosFuncionario() {
            Object.values(mapaCamposFuncionario).forEach(function (id) {
                const elemento = document.getElementById(id);
                const erroElemento = document.getElementById(id + 'Erro');

                if (elemento) elemento.classList.remove('is-invalid');
                if (erroElemento) erroElemento.innerText = '';
            });
        }

        const btnCadastrar = document.getElementById('btnCadastrarFuncionario');
        if (btnCadastrar) {
            btnCadastrar.addEventListener('click', function () {
                limparErrosFuncionario();
                document.getElementById('funcionarioModalTitulo').innerText = 'Cadastrar funcionário';
                document.getElementById('formFuncionario').action = "{{ route('funcionarios.store') }}";
                document.getElementById('metodoSpoof').innerHTML = '';

                // Limpando os campos para um novo cadastro
                document.getElementById('funcionarioUsuario').value = '';
                document.getElementById('funcionarioCpf').value = '';
                document.getElementById('funcionarioRg').value = '';
                document.getElementById('funcionarioSexo').value = '';
                document.getElementById('funcionarioDataNascimento').value = '';
                document.getElementById('funcionarioSalario').value = '';
                document.getElementById('funcionarioPerfil').value = '';
                document.getElementById('funcionarioSenha').value = '';

                document.getElementById('funcionarioSenha').required = true;
                document.getElementById('funcionarioSenhaLabel').innerText = 'Senha *';
                const senhaAjuda = document.getElementById('funcionarioSenhaAjuda');
                if (senhaAjuda) senhaAjuda.style.display = 'none';
            });
        }

        document.querySelectorAll('.btn-editar-funcionario').forEach(function (botao) {
            botao.addEventListener('click', function () {
                limparErrosFuncionario();
                document.getElementById('funcionarioModalTitulo').innerText = 'Editar funcionário';
                document.getElementById('formFuncionario').action = '/funcionarios/' + botao.dataset.id;

                // Correção do Spoofing para formulários dinâmicos no Laravel
                document.getElementById('metodoSpoof').innerHTML = '<input type="hidden" name="_method" value="PUT">';

                // Preenchendo os campos com os dados do banco (via dataset do botão)
                document.getElementById('funcionarioUsuario').value = botao.dataset.usuario;
                document.getElementById('funcionarioCpf').value = botao.dataset.cpf;
                document.getElementById('funcionarioRg').value = botao.dataset.rg;
                document.getElementById('funcionarioSexo').value = botao.dataset.sexo;
                document.getElementById('funcionarioDataNascimento').value = botao.dataset.dataNascimento;
                document.getElementById('funcionarioSalario').value = botao.dataset.salario;
                document.getElementById('funcionarioPerfil').value = botao.dataset.perfil;
                document.getElementById('funcionarioSenha').value = '';

                document.getElementById('funcionarioSenha').required = false;
                document.getElementById('funcionarioSenhaLabel').innerText = 'Senha';
                const senhaAjuda = document.getElementById('funcionarioSenhaAjuda');
                if (senhaAjuda) senhaAjuda.style.display = 'block';
            });
        });

        document.getElementById('formFuncionario').addEventListener('submit', async function (evento) {
            evento.preventDefault();
            limparErrosFuncionario();

            const form = evento.target;
            const botaoSalvar = document.getElementById('funcionarioBotaoSalvar');
            const textoOriginalBotao = botaoSalvar.innerText;

            botaoSalvar.disabled = true;
            botaoSalvar.innerText = 'Salvando...';

            try {
                const resposta = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || ''
                    },
                    body: new FormData(form),
                });

                if (resposta.status === 422) {
                    const corpo = await resposta.json();

                    for (const campo in corpo.errors) {
                        const idCampo = mapaCamposFuncionario[campo];
                        if (idCampo && document.getElementById(idCampo)) {
                            document.getElementById(idCampo).classList.add('is-invalid');
                            document.getElementById(idCampo + 'Erro').innerText = corpo.errors[campo][0];
                        }
                    }
                    return;
                }

                if (resposta.ok) {
                    window.location.reload();
                    return;
                }

                throw new Error('Falha na requisição ao servidor.');

            } catch (erro) {
                alert('Não foi possível processar a solicitação. Verifique sua conexão ou tente novamente mais tarde.');
            } finally {
                botaoSalvar.disabled = false;
                botaoSalvar.innerText = textoOriginalBotao;
            }
        });
    });
</script>
