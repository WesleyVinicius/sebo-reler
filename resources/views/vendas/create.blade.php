@extends('layouts.app')

@section('titulo', 'Nova venda')
@section('subtitulo', 'Informe o CPF do cliente e leia o código dos exemplares')

@section('conteudo')

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $erro)
                <div>{{ $erro }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('vendas.store') }}" id="formVenda"
          data-faixas="{{ $faixas->toJson() }}"
          data-url-cliente="{{ url('vendas/cliente') }}"
          data-url-exemplar="{{ url('vendas/exemplar') }}">
        @csrf

        <input type="hidden" name="id_cliente" id="idCliente">
        <div id="areaExemplares"></div>

        <div class="row g-3">

            {{-- COLUNA ESQUERDA --}}
            <div class="col-lg-8">

                <div class="panel mb-3">
                    <h3>1. Cliente</h3>

                    <div id="blocoBuscaCliente">
                        <div class="input-group">
                            <input type="text" id="cpf" class="form-control" placeholder="Digite o CPF do cliente e tecle Enter" inputmode="numeric" autofocus>
                            <button type="button" class="btn btn-accent" id="btnBuscarCliente">
                                <i class="bi bi-search"></i> Buscar
                            </button>
                        </div>
                        <div id="msgCliente" class="small text-danger mt-2"></div>
                    </div>

                    <div id="blocoClienteEncontrado" class="d-none d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold fs-5" id="clienteNome"></div>
                            <div class="text-muted small">CPF <span id="clienteCpf"></span></div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnTrocarCliente">
                            <i class="bi bi-arrow-repeat"></i> Trocar
                        </button>
                    </div>
                </div>

                <div class="panel">
                    <h3>2. Exemplares</h3>

                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                        <input type="text" id="codigo" class="form-control" placeholder="Código do exemplar (tecle Enter para adicionar)" inputmode="numeric" disabled>
                    </div>
                    <div id="msgCodigo" class="small text-danger mt-2"></div>

                    <table class="table panel-table align-middle mt-3">
                        <thead>
                        <tr>
                            <th>Cód.</th>
                            <th>Título</th>
                            <th>Autor(es)</th>
                            <th>Conservação</th>
                            <th class="text-end">Preço</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody id="corpoItens"></tbody>
                    </table>
                </div>
            </div>

            {{-- COLUNA DIREITA: RESUMO --}}
            <div class="col-lg-4">
                <div class="panel">
                    <h3>Resumo</h3>

                    <div class="mb-3">
                        <div class="text-muted small">Cliente</div>
                        <div id="resCliente" class="fw-semibold">—</div>
                    </div>

                    <table class="table table-sm mb-3">
                        <tr><td class="text-muted">Itens</td><td class="text-end" id="resItens">0</td></tr>
                        <tr><td class="text-muted">Subtotal</td><td class="text-end" id="resSubtotal">R$ 0,00</td></tr>
                        <tr><td class="text-muted">Desconto (<span id="resPercentual">0</span>%)</td><td class="text-end text-success" id="resDesconto">- R$ 0,00</td></tr>
                        <tr class="fw-bold fs-5"><td>Total</td><td class="text-end" id="resTotal">R$ 0,00</td></tr>
                    </table>

                    <button type="submit" class="btn btn-accent w-100 mb-2" id="btnFinalizar" disabled>
                        <i class="bi bi-check-lg"></i> Finalizar venda
                    </button>
                    <a href="{{ route('vendas.index') }}" class="btn btn-outline-secondary w-100">Cancelar</a>
                </div>
            </div>

        </div>
    </form>

@endsection

@section('scripts')
    <script>
        const form = document.getElementById('formVenda');
        const faixas = JSON.parse(form.dataset.faixas);
        const urlCliente = form.dataset.urlCliente;
        const urlExemplar = form.dataset.urlExemplar;

        const el = (id) => document.getElementById(id);
        const moeda = (v) => v.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

        let cliente = null;   // { id, nome, cpf }
        let itens = [];       // [{ id, titulo, autores, conservacao, preco }]

        async function buscar(url) {
            const resposta = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const dados = await resposta.json();
            return { ok: resposta.ok, dados: dados };
        }

        // ---------- CLIENTE ----------
        async function buscarCliente() {
            const cpf = el('cpf').value.trim();
            el('msgCliente').textContent = '';
            if (cpf === '') return;

            const r = await buscar(urlCliente + '/' + encodeURIComponent(cpf));
            if (!r.ok) {
                el('msgCliente').textContent = r.dados.mensagem;
                return;
            }

            cliente = r.dados;
            render();
            el('codigo').focus();
        }

        function trocarCliente() {
            cliente = null;
            el('cpf').value = '';
            render();
            el('cpf').focus();
        }

        // ---------- EXEMPLARES ----------
        async function adicionarExemplar() {
            const codigo = el('codigo').value.trim();
            el('msgCodigo').textContent = '';
            if (codigo === '') return;

            if (itens.some((i) => String(i.id) === codigo)) {
                el('msgCodigo').textContent = 'Este exemplar já está na venda.';
                el('codigo').select();
                return;
            }

            const r = await buscar(urlExemplar + '/' + encodeURIComponent(codigo));
            if (!r.ok) {
                el('msgCodigo').textContent = r.dados.mensagem || 'Código inválido.';
                el('codigo').select();
                return;
            }

            itens.push(r.dados);
            el('codigo').value = '';
            render();
            el('codigo').focus();
        }

        function removerItem(id) {
            itens = itens.filter((i) => i.id !== id);
            render();
            el('codigo').focus();
        }

        // ---------- DESENHO DA TELA ----------
        function render() {
            // bloco do cliente
            el('blocoBuscaCliente').classList.toggle('d-none', cliente !== null);
            el('blocoClienteEncontrado').classList.toggle('d-none', cliente === null);
            el('codigo').disabled = (cliente === null);
            el('idCliente').value = cliente ? cliente.id : '';
            el('clienteNome').textContent = cliente ? cliente.nome : '';
            el('clienteCpf').textContent = cliente ? cliente.cpf : '';
            el('resCliente').textContent = cliente ? cliente.nome : '—';

            // tabela de itens (textContent evita injetar HTML vindo do banco)
            const corpo = el('corpoItens');
            corpo.innerHTML = '';
            itens.forEach(function (item) {
                const tr = document.createElement('tr');
                [item.id, item.titulo, item.autores, item.conservacao, moeda(item.preco)].forEach(function (texto, idx) {
                    const td = document.createElement('td');
                    td.textContent = texto;
                    if (idx === 4) td.className = 'text-end';
                    tr.appendChild(td);
                });

                const tdAcao = document.createElement('td');
                tdAcao.className = 'text-end';
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-sm btn-outline-danger';
                btn.innerHTML = '<i class="bi bi-x-lg"></i>';
                btn.addEventListener('click', () => removerItem(item.id));
                tdAcao.appendChild(btn);
                tr.appendChild(tdAcao);

                corpo.appendChild(tr);
            });

            if (itens.length === 0) {
                const tr = document.createElement('tr');
                tr.innerHTML = '<td colspan="6" class="text-muted text-center py-4">Nenhum exemplar adicionado.</td>';
                corpo.appendChild(tr);
            }

            // inputs ocultos que o servidor vai receber (exemplares[])
            el('areaExemplares').innerHTML = '';
            itens.forEach(function (item) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'exemplares[]';
                input.value = item.id;
                el('areaExemplares').appendChild(input);
            });

            // totais
            const subtotal = itens.reduce((soma, i) => soma + i.preco, 0);

            let percentual = 0;
            let maiorMinimo = -1;
            faixas.forEach(function (faixa) {
                const minimo = Number(faixa.valor_minimo);
                if (subtotal > minimo && minimo > maiorMinimo) {
                    maiorMinimo = minimo;
                    percentual = Number(faixa.porcent_desconto);
                }
            });
            const desconto = Math.round(subtotal * percentual) / 100;

            el('resItens').textContent = itens.length;
            el('resSubtotal').textContent = moeda(subtotal);
            el('resPercentual').textContent = percentual;
            el('resDesconto').textContent = '- ' + moeda(desconto);
            el('resTotal').textContent = moeda(subtotal - desconto);

            el('btnFinalizar').disabled = !(cliente && itens.length > 0);
        }

        // ---------- EVENTOS ----------
        // Enter nesses campos NÃO pode enviar o formulário: ele faz a busca
        el('cpf').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); buscarCliente(); }
        });
        el('codigo').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); adicionarExemplar(); }
        });
        el('btnBuscarCliente').addEventListener('click', buscarCliente);
        el('btnTrocarCliente').addEventListener('click', trocarCliente);

        render();
    </script>
@endsection
