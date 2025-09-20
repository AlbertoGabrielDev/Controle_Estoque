@extends('layouts.principal')

@section('conteudo')
    <div class="bg-white p-4 md:p-6 rounded-md shadow-md w-full max-w-7xl mx-auto">
        <header class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-center md:text-left">Registrar Venda</h1>
        </header>

        <!-- Cliente + Carrinho -->
        <section class="mt-2 mb-6 grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
            <div class="md:col-span-2">
                <label for="client-input" class="block text-sm font-medium text-gray-700">Cliente (WhatsApp)</label>
                <input id="client-input" type="text" maxlength="20" inputmode="numeric" pattern="[0-9]*"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2"
                    placeholder="Ex.: 5599999999999" aria-label="Telefone do cliente">
            </div>
            <div class="flex gap-2">
                <button id="carregar-carrinho"
                    class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-base font-medium">
                    Carregar Carrinho
                </button>
                <button id="toggle-manual"
                    class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-base font-medium"
                    aria-expanded="false" aria-controls="manual-area">
                    Inserir código
                </button>
            </div>
        </section>

        <!-- Inserção manual -->
        <section id="manual-area" class="hidden">
            <div class="bg-gray-50 border rounded-lg p-3 md:p-4">
                <div class="flex flex-col md:flex-row items-end gap-3">
                    <div class="w-full md:w-80">
                        <label for="codigo-manual" class="block text-sm font-medium text-gray-700">Código do produto</label>
                        <input id="codigo-manual" type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2"
                            placeholder="Ex.: ABC123" aria-label="Código do produto">
                    </div>
                    <div class="flex gap-2 w-full md:w-auto">
                        <button id="adicionar-manual"
                            class="flex-1 md:flex-none bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                            Adicionar
                        </button>
                        <button id="fechar-manual"
                            class="flex-1 md:flex-none bg-white border hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg">
                            Fechar
                        </button>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500">Dica: <kbd class="px-1 py-0.5 border rounded">Enter</kbd> adiciona,
                    <kbd class="px-1 py-0.5 border rounded">Esc</kbd> fecha.
                </p>
            </div>
        </section>

        <!-- Scanner -->
        <section class="mt-6">
            <div class="flex flex-col md:flex-row md:items-center gap-3">
                <div class="flex gap-2">
                    <button id="start-button"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Iniciar Câmera</button>
                    <button id="stop-button"
                        class="hidden bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">Parar Câmera</button>
                </div>
                <div class="text-sm text-gray-500">Aponte a câmera para o QR Code do produto.</div>
            </div>
            <div id="qr-reader" class="w-full max-w-sm md:max-w-md h-72 mt-4 hidden mx-auto md:mx-0"></div>
        </section>

        <!-- Carrinho -->
        <section class="mt-8 border rounded-lg p-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold">Carrinho de Compras</h2>
                <button id="finalizar-venda"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
                    Finalizar Venda
                </button>
            </div>

            <div id="carrinho-vazio" class="text-center py-6 text-gray-500">Nenhum produto adicionado ao carrinho</div>

            <div id="carrinho-itens" class="hidden">
                <!-- Mobile -->
                <div id="lista-produtos-mobile" class="space-y-3 md:hidden mt-4"></div>
                <!-- Desktop -->
                <div class="hidden md:block overflow-auto mt-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Produto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Preço</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Quantidade</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ações</th>
                            </tr>
                        </thead>
                        <tbody id="lista-produtos" class="bg-white divide-y divide-gray-200"></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-right font-semibold">Total do Carrinho:</td>
                                <td class="px-4 py-4 font-bold" id="total-carrinho">R$ 0,00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </section>

        <!-- Histórico (mantive como estava) -->
        <section class="mt-8">
            <h2 class="text-xl md:text-2xl font-bold mb-4 text-center md:text-left">Últimos Produtos Vendidos</h2>
            <div class="overflow-x-auto">
                <table class="table-auto w-full border-collapse border border-gray-200 rounded-md shadow-sm">
                    <thead class="bg-gray-100">
                        <tr class="text-sm md:text-base text-gray-600">
                            <th class="py-3 px-4 md:px-6 text-left font-medium">Nome</th>
                            <th class="py-3 px-4 md:px-6 text-left font-medium">Preço Venda</th>
                            <th class="py-3 px-4 md:px-6 text-left font-medium">Cod. Produto</th>
                            <th class="py-3 px-4 md:px-6 text-left font-medium">Quantidade</th>
                            <th class="py-3 px-4 md:px-6 text-left font-medium">Vendedor</th>
                        </tr>
                    </thead>
                    <tbody id="historico-produtos">
                        @foreach ($vendas as $venda)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 text-sm md:text-base">
                                <td class="py-3 px-4 md:px-6 text-gray-700">{{ $venda->produto->nome_produto }}</td>
                                <td class="py-3 px-4 md:px-6 text-gray-700">R$
                                    {{ number_format($venda->preco_venda, 2, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 md:px-6 text-gray-700">{{ $venda->produto->cod_produto }}</td>
                                <td class="py-3 px-4 md:px-6 text-gray-700">{{ $venda->quantidade }}</td>
                                <td class="py-3 px-4 md:px-6 text-gray-700">{{ $venda->usuario->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-6 flex justify-center">
                    <nav class="flex items-center space-x-2">
                        <a href="{{ $vendas->previousPageUrl() }}"
                            class="py-2 px-3 bg-gray-100 border rounded-l-lg hover:bg-gray-200 text-gray-600"
                            aria-label="Página anterior">
                            <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                        <span class="py-2 px-3 bg-gray-100 text-gray-600"
                            aria-current="page">{{ $vendas->currentPage() }}</span>
                        <a href="{{ $vendas->nextPageUrl() }}"
                            class="py-2 px-3 bg-gray-100 border rounded-r-lg hover:bg-gray-200 text-gray-600"
                            aria-label="Próxima página">
                            <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    </nav>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js"></script>
    <script>
        (() => {
            'use strict';

            const CSRF = "{{ csrf_token() }}";
            const qs = (sel) => document.querySelector(sel);
            const qsa = (sel) => document.querySelectorAll(sel);
            const on = (el, ev, fn) => { if (el) el.addEventListener(ev, fn); };

            // pegue a referência do jQuery pela janela (não conflita com qs/qsa)
            const jq = window.jQuery || window.$;
            if (!jq || !jq.ajax) {
                console.error('jQuery não encontrado. Inclua jQuery ou troque post() por fetch().');
            }

            // use SEMPRE jq.ajax aqui
            const post = (url, data) => jq.ajax({
                url,
                method: 'POST',
                data: { _token: CSRF, ...data }
            });

            /* Utils */
            const fmt = (v) => 'R$ ' + (parseFloat(v || 0)).toFixed(2).replace('.', ',');
            const getClient = () => (qs('#client-input')?.value || '').trim();
            const needClient = async () => {
                const current = getClient();
                if (current) return current;
                const c = prompt('Informe o telefone do cliente (WhatsApp):', '') || '';
                if (c.trim()) {
                    const inp = qs('#client-input');
                    if (inp) inp.value = c.trim();
                    return c.trim();
                }
                return null;
            };

            /* Estado */
            const state = { cart: null, staged: [] }; // staged = itens locais quando não há client

            /* DOM */
            const dom = {
                manual: {
                    toggle: qs('#toggle-manual'),
                    area: qs('#manual-area'),
                    input: qs('#codigo-manual'),
                    add: qs('#adicionar-manual'),
                    close: qs('#fechar-manual'),
                },
                scan: {
                    start: qs('#start-button'),
                    stop: qs('#stop-button'),
                    box: qs('#qr-reader'),
                    scanner: null,
                },
                cart: {
                    load: qs('#carregar-carrinho'),
                    empty: qs('#carrinho-vazio'),
                    wrap: qs('#carrinho-itens'),
                    listD: qs('#lista-produtos'),
                    listM: qs('#lista-produtos-mobile'),
                    total: qs('#total-carrinho'),
                    ok: qs('#finalizar-venda'),
                }
            };

            /* UI helpers */
            const show = (el, v) => el.classList.toggle('hidden', !v);
            const openManual = () => { show(dom.manual.area, true); dom.manual.toggle.setAttribute('aria-expanded', 'true'); setTimeout(() => dom.manual.input.focus(), 50); };
            const closeManual = () => { show(dom.manual.area, false); dom.manual.toggle.setAttribute('aria-expanded', 'false'); };

            /* Busca produto (QR ou código) */
            const fetchProduto = async ({ qr = null, codigo = null }) => {
                const payload = qr ? { codigo_qr: qr } : { codigo_produto: codigo };
                const r = await post('/verdurao/vendas/buscar-produto', payload);
                if (!r.success || !r.produto) throw new Error('Produto não encontrado');
                return r.produto;
            };

            /* Adicionar produto (reuso) */
            const addProduto = async (p) => {
                if (getClient()) {
                    const add = await post('/verdurao/vendas/carrinho/adicionar', { client: getClient(), id_produto: p.id_produto, quantidade: 1 });
                    if (add && add.cart) { state.cart = add.cart; render(); toastr.success('Produto adicionado!', 'Sucesso'); }
                } else {
                    const x = state.staged.find(i => i.id_produto === p.id_produto);
                    x ? x.quantidade++ : state.staged.push({
                        _localId: Date.now() + Math.random(),
                        id_produto: p.id_produto, nome: p.nome_produto, preco: +p.preco_venda || 0,
                        quantidade: 1, cod_produto: p.cod_produto, unidade_medida: p.unidade_medida, estoque_disponivel: p.estoque_atual
                    });
                    render(); toastr.info('Produto adicionado (pendente de cliente).', 'Info');
                }
            };

            /* Carrinho */
            const loadCart = async () => {
                const client = getClient();
                if (!client) { toastr.warning('Informe o cliente para carregar o carrinho.', 'Aviso'); return null; }
                const r = await post('/verdurao/vendas/carrinho', { client });
                if (r.success && r.cart) { state.cart = r.cart; render(); toastr.success('Carrinho carregado.', 'OK'); return r.cart; }
                return null;
            };

            const loadCartMerge = async () => {
                const c = getClient() || await needClient(); if (!c) return;
                const cart = await loadCart(); if (!cart) return;
                if (state.staged.length) {
                    for (const it of state.staged) await post('/verdurao/vendas/carrinho/adicionar', { client: c, id_produto: it.id_produto, quantidade: it.quantidade });
                    state.staged = []; await loadCart();
                }
            };

            const updateQty = async ({ itemId = null, localId = null, delta = 0 }) => {
                if (itemId && state.cart) {
                    const it = (state.cart.items || []).find(i => i.id === itemId); if (!it) return;
                    const nova = Math.max(0, +it.quantidade + delta);
                    const r = await post('/verdurao/vendas/carrinho/quantidade', { client: getClient(), cart_item_id: itemId, quantidade: nova });
                    if (r.success && r.cart) { state.cart = r.cart; render(); toastr.success('Quantidade atualizada.', 'Sucesso'); }
                    else toastr.error(r.message || 'Falha ao atualizar.', 'Erro');
                } else if (localId) {
                    const it = state.staged.find(i => i._localId === localId); if (!it) return;
                    it.quantidade += delta; if (it.quantidade <= 0) state.staged = state.staged.filter(i => i._localId !== localId);
                    render();
                }
            };

            const removeItem = async ({ itemId = null, localId = null }) => {
                if (itemId) {
                    const r = await post('/verdurao/vendas/carrinho/remover', { client: getClient(), cart_item_id: itemId });
                    if (r.success && r.cart) { state.cart = r.cart; render(); toastr.info('Produto removido.', 'Info'); }
                    else toastr.error(r.message || 'Falha ao remover.', 'Erro');
                } else if (localId) {
                    state.staged = state.staged.filter(i => i._localId !== localId); render(); toastr.info('Produto removido.', 'Info');
                }
            };

            const finalize = async () => {
                const c = getClient() || await needClient(); if (!c) return;
                const cart = await loadCart(); if (!cart) return;
                if (state.staged.length) {
                    for (const it of state.staged) await post('/verdurao/vendas/carrinho/adicionar', { client: c, id_produto: it.id_produto, quantidade: it.quantidade });
                    state.staged = [];
                }
                const cart2 = await loadCart(); if (!cart2) return;
                try {
                    const r = await post('/verdurao/vendas/registrar-venda', { client: c });
                    if (r.success) { toastr.success('Venda registrada!', 'Sucesso'); state.cart = null; state.staged = []; render(); }
                    else {
                        const det = (r.detalhes || []).map(d => `Produto ${d.id_produto}: solicitado ${d.quantidade_solicitada ?? '-'} / disponível ${d.estoque_atual ?? '-'}`).join('<br>');
                        toastr.error((r.message || 'Falha ao finalizar') + (det ? '<br>' + det : ''), 'Erro');
                    }
                } catch (e) {
                    const r = e.responseJSON;
                    const det = r ? (r.detalhes || r.produtos_sem_estoque || []).map(d => `Produto ${d.id_produto}: solicitado ${d.quantidade_solicitada ?? '-'} / disponível ${d.estoque_atual ?? '-'}`).join('<br>') : '';
                    toastr.error((r?.message || 'Erro ao processar a venda.') + (det ? '<br>' + det : ''), 'Erro');
                }
            };

            /* Render (gera desktop + mobile numa passada) */
            const render = () => {
                const hasCart = !!state.cart;
                const items = hasCart ? (state.cart.items || []) : state.staged;
                const has = items.length > 0;
                show(dom.cart.empty, !has);
                show(dom.cart.wrap, has);

                let rowsD = '', rowsM = '', total = 0;
                for (const it of items) {
                    const local = !hasCart;
                    const nome = local ? it.nome : it.nome_produto;
                    const preco = local ? it.preco : +it.preco_unit || 0;
                    const qtd = +it.quantidade;
                    const linha = local ? (it.preco * it.quantidade) : (+it.subtotal_valor || (preco * qtd));
                    total += linha;
                    const idAttr = local ? `data-lkey="${it._localId}"` : `data-itemid="${it.id}"`;

                    rowsD += `
                <tr>
                  <td class="px-4 py-3">${nome}</td>
                  <td class="px-4 py-3">${fmt(preco)}</td>
                  <td class="px-4 py-3">
                    <div class="inline-flex items-center border rounded-md overflow-hidden">
                      <button class="quantity-btn px-3 py-1" data-action="dec" ${idAttr}>-</button>
                      <span class="px-4 py-1 select-none">${qtd}</span>
                      <button class="quantity-btn px-3 py-1" data-action="inc" ${idAttr}>+</button>
                    </div>
                  </td>
                  <td class="px-4 py-3">${fmt(linha)}</td>
                  <td class="px-4 py-3">
                    <button class="remove-btn text-red-600 hover:text-red-700" ${idAttr}>Remover</button>
                  </td>
                </tr>`;

                    rowsM += `
                <article class="border rounded-lg p-3">
                  <div class="flex justify-between">
                    <h3 class="font-medium text-gray-800">${nome}</h3>
                    <div class="font-semibold text-gray-800">${fmt(linha)}</div>
                  </div>
                  <div class="mt-1 text-sm text-gray-600">Preço: ${fmt(preco)}</div>
                  <div class="mt-3 flex items-center justify-between">
                    <div class="inline-flex items-center border rounded-md overflow-hidden">
                      <button class="quantity-btn px-3 py-2" data-action="dec" ${idAttr}>-</button>
                      <span class="px-4 py-2 select-none">${qtd}</span>
                      <button class="quantity-btn px-3 py-2" data-action="inc" ${idAttr}>+</button>
                    </div>
                    <button class="remove-btn text-red-600 hover:text-red-700" ${idAttr}>Remover</button>
                  </div>
                </article>`;
                }

                dom.cart.listD.innerHTML = rowsD;
                dom.cart.listM.innerHTML = rowsM;
                dom.cart.total.textContent = fmt(hasCart ? (state.cart.total_valor || 0) : total);
            };

            /* Eventos base */
            on(dom.cart.load, 'click', loadCartMerge);
            on(dom.cart.ok, 'click', finalize);

            // eventos delegados (+/−/remover) — um só para desktop e mobile
            const delegate = (root, sel, type, handler) => root.addEventListener(type, e => {
                const t = e.target.closest(sel); if (t && root.contains(t)) handler(e, t);
            });
            const qtyHandler = (e, el) => {
                const inc = el.dataset.action === 'inc';
                const itemId = el.dataset.itemid ? +el.dataset.itemid : null;
                const localId = el.dataset.lkey ? el.dataset.lkey : null;
                updateQty({ itemId, localId, delta: inc ? 1 : -1 });
            };
            const remHandler = (e, el) => {
                const itemId = el.dataset.itemid ? +el.dataset.itemid : null;
                const localId = el.dataset.lkey ? el.dataset.lkey : null;
                removeItem({ itemId, localId });
            };
            [dom.cart.listD, dom.cart.listM].forEach(root => {
                delegate(root, '.quantity-btn', 'click', qtyHandler);
                delegate(root, '.remove-btn', 'click', remHandler);
            });

            /* Inserção manual (compacto) */
            on(dom.manual.toggle, 'click', () => dom.manual.area.classList.contains('hidden') ? openManual() : closeManual());
            on(dom.manual.close, 'click', closeManual);
            on(dom.manual.add, 'click', async () => {
                const code = dom.manual.input.value.trim();
                if (!code) return toastr.warning('Digite um código de produto.', 'Aviso');
                try { const p = await fetchProduto({ codigo: code }); await addProduto(p); dom.manual.input.value = ''; dom.manual.input.focus(); }
                catch { toastr.error('Produto não encontrado.', 'Erro'); }
            });
            on(dom.manual.input, 'keydown', (e) => {
                if (e.key === 'Enter') dom.manual.add.click();
                if (e.key === 'Escape') closeManual();
            });

            /* Scanner (compacto) */
            on(dom.scan.start, 'click', async () => {
                if (dom.scan.scanner) await stopScan();
                await startScan();
            });
            on(dom.scan.stop, 'click', stopScan);
            document.addEventListener('keydown', (e) => (e.key === 'Escape' && dom.scan.scanner) ? stopScan() : null);

            async function startScan() {
                dom.scan.scanner = new Html5Qrcode("qr-reader");
                show(dom.scan.box, true); show(dom.scan.start, false); show(dom.scan.stop, true);
                await dom.scan.scanner.start({ facingMode: "environment" }, { fps: 10, qrbox: { width: 300, height: 300 } },
                    async (text) => { try { const p = await fetchProduto({ qr: text }); await addProduto(p); } catch { toastr.error('Produto não encontrado.', 'Erro'); } finally { stopScan(); } },
                    () => { });
            }
            async function stopScan() {
                if (!dom.scan.scanner) return;
                await dom.scan.scanner.stop().catch(() => { });
                dom.scan.scanner = null;
                show(dom.scan.box, false); show(dom.scan.start, true); show(dom.scan.stop, false);
            }

            // boot
            render(); // estado inicial
        })();
    </script>
@endsection