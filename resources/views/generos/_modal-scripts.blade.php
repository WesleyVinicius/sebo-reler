<script>
    document.getElementById('btnCadastrarGenero').addEventListener('click', function () {
        document.getElementById('generoModalTitulo').innerText = 'Cadastrar genero';
        document.getElementById('formGenero').action = "{{ route('generos.store') }}";
        document.getElementById('metodoSpoof').innerHTML = '';
        document.getElementById('generoNome').value = '';
    });

    document.querySelectorAll('.btn-editar-genero').forEach(function (botao) {
        botao.addEventListener('click', function () {
            document.getElementById('generoModalTitulo').innerText = 'Editar genero';
            document.getElementById('formGenero').action = '/generos/' + botao.dataset.id;
            document.getElementById('metodoSpoof').innerHTML = '@method('PUT')';
            document.getElementById('generoNome').value = botao.dataset.nome;
        });
    });
</script>
