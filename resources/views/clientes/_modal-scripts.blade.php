<script>
    document.getElementById('btnCadastrarCliente').addEventListener('click', function () {
        document.getElementById('clienteModalTitulo').innerText = 'Cadastrar cliente';
        document.getElementById('formCliente').action = "{{ route('clientes.store') }}";
        document.getElementById('metodoSpoof').innerHTML = '';
        document.getElementById('clienteNome').value = '';
        document.getElementById('clienteCpf').value = '';
    });

    document.querySelectorAll('.btn-editar-cliente').forEach(function (botao) {
        botao.addEventListener('click', function () {
            document.getElementById('clienteModalTitulo').innerText = 'Editar cliente';
            document.getElementById('formCliente').action = '/clientes/' + botao.dataset.id;
            document.getElementById('metodoSpoof').innerHTML = '@method('PUT')';
            document.getElementById('clienteNome').value = botao.dataset.nome;
            document.getElementById('clienteCpf').value = botao.dataset.cpf;
        });
    });
</script>
