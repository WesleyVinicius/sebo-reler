<script>
    document.getElementById('btnCadastrarLivro').addEventListener('click', function () {
        document.getElementById('livroModalTitulo').innerText = 'Cadastrar livro';
        document.getElementById('formLivro').action = "{{ route('livros.store') }}";
        document.getElementById('metodoSpoof').innerHTML = '';

        document.getElementById('livroTitulo').value = '';
        document.getElementById('livroEditora').value = '';
        document.getElementById('livroGenero').value = '';
        document.getElementById('livroConservacao').value = '';
        document.getElementById('livroPrecoCompra').value = '';
        document.getElementById('livroPrecoVenda').value = '';
        document.getElementById('livroQuantidade').value = 1;

        // Desmarca todas as opções de autor
        Array.from(document.getElementById('livroAutores').options).forEach(function (opcao) {
            opcao.selected = false;
        });

        // No cadastro, mostra o campo de quantidade
        document.getElementById('livroQuantidadeGrupo').style.display = 'block';
        document.getElementById('livroQuantidade').required = true;
    });

    document.querySelectorAll('.btn-editar-livro').forEach(function (botao) {
        botao.addEventListener('click', function () {
            document.getElementById('livroModalTitulo').innerText = 'Editar livro';
            document.getElementById('formLivro').action = '/livros/' + botao.dataset.id;
            document.getElementById('metodoSpoof').innerHTML = '@method('PUT')';

            document.getElementById('livroTitulo').value = botao.dataset.titulo;
            document.getElementById('livroEditora').value = botao.dataset.idEditora;
            document.getElementById('livroGenero').value = botao.dataset.idGenero;
            document.getElementById('livroConservacao').value = botao.dataset.idConservacao;
            document.getElementById('livroPrecoCompra').value = botao.dataset.precoCompra;
            document.getElementById('livroPrecoVenda').value = botao.dataset.precoVenda;

            // Decodifica o JSON de IDs de autores (ex: "[1,3]") e marca as opções correspondentes
            const idsAutores = JSON.parse(botao.dataset.autores);
            Array.from(document.getElementById('livroAutores').options).forEach(function (opcao) {
                opcao.selected = idsAutores.includes(Number(opcao.value));
            });

            // Na edição, não faz sentido "adicionar exemplares" — esconde o campo
            document.getElementById('livroQuantidadeGrupo').style.display = 'none';
            document.getElementById('livroQuantidade').required = false;
        });
    });
</script>
