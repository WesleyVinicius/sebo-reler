<script>
    document.getElementById('btnCadastrarEditora').addEventListener('click', function () {
        document.getElementById('editoraModalTitulo').innerText = 'Cadastrar Editora';
        document.getElementById('formEditora').action = "{{ route('editoras.store') }}";
        document.getElementById('metodoSpoof').innerHTML = '';
        document.getElementById('editoraNome').value = '';
    });

    document.querySelectorAll('.btn-editar-editora').forEach(function (botao) {
        botao.addEventListener('click', function () {
            document.getElementById('editoraModalTitulo').innerText = 'Editar Editora';
            document.getElementById('formEditora').action = '/editoras/' + botao.dataset.id;
            document.getElementById('metodoSpoof').innerHTML = '@method('PUT')';
            document.getElementById('editoraNome').value = botao.dataset.nome;
        });
    });
</script>
