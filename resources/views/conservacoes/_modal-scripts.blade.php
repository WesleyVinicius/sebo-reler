<script>
    document.getElementById('btnCadastrarConservacao').addEventListener('click', function () {
        document.getElementById('conservacaoModalTitulo').innerText = 'Cadastrar Estado de Conservação';
        document.getElementById('formConservacao').action = "{{ route('conservacoes.store') }}";
        document.getElementById('metodoSpoof').innerHTML = '';
        document.getElementById('conservacaoEstado').value = '';
    });

    document.querySelectorAll('.btn-editar-conservacao').forEach(function (botao) {
        botao.addEventListener('click', function () {
            document.getElementById('conservacaoModalTitulo').innerText = 'Editar estado';
            document.getElementById('formConservacao').action = '/conservacoes/' + botao.dataset.id;
            document.getElementById('metodoSpoof').innerHTML = '@method('PUT')';
            document.getElementById('conservacaoEstado').value = botao.dataset.estado;
        });
    });
</script>
