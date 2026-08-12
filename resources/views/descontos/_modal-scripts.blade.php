<script>
    document.getElementById('btnCadastrarDesconto').addEventListener('click', function () {
        document.getElementById('descontoModalTitulo').innerText = 'Cadastrar desconto';
        document.getElementById('formDesconto').action = "{{ route('descontos.store') }}";
        document.getElementById('metodoSpoof').innerHTML = '';
        document.getElementById('descontoValorMinimo').value = '';
        document.getElementById('descontoPorcentual').value = '';
    });

    document.querySelectorAll('.btn-editar-desconto').forEach(function (botao) {
        botao.addEventListener('click', function () {
            document.getElementById('descontoModalTitulo').innerText = 'Editar desconto';
            document.getElementById('formDesconto').action = '/descontos/' + botao.dataset.id;
            document.getElementById('metodoSpoof').innerHTML = '@method('PUT')';
            document.getElementById('descontoValorMinimo').value = botao.dataset.valorMinimo;
            document.getElementById('descontoPorcentual').value = botao.dataset.porcentDesconto;
        });
    });
</script>
