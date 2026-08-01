<script>
    document.getElementById('btnCadastrarAutor').addEventListener('click', function () {
        document.getElementById('autorModalTitulo').innerText = 'Cadastrar Autor';
        document.getElementById('formAutor').action = "{{ route('autores.store') }}";
        document.getElementById('metodoSpoof').innerHTML = '';
        document.getElementById('autorNome').value = '';
    });

    document.querySelectorAll('.btn-editar-autor').forEach(function (botao) {
        botao.addEventListener('click', function () {
            document.getElementById('autorModalTitulo').innerText = 'Editar Autor';
            document.getElementById('formAutor').action = '/autores/' + botao.dataset.id;
            document.getElementById('metodoSpoof').innerHTML = '@method('PUT')';
            document.getElementById('autorNome').value = botao.dataset.nome;
        });
    });
</script>
