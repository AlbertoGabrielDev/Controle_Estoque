@extends('layouts.principal')

@section('conteudo')
    <div class="bg-white p-6 rounded-md shadow-md w-full max-w-7xl mx-auto">
        <h1 class="text-2xl md:text-3xl font-bold mb-6 text-center">Registrar Venda</h1>
        <div class="mt-2 mb-6 flex flex-col md:flex-row md:items-end gap-3">
            <div class="w-full md:w-1/3">
                <label for="client-input" class="block text-sm font-medium text-gray-700">Cliente (WhatsApp)</label>
                <input id="client-input" type="text" maxlength="20"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2"
                    placeholder="Ex.: 5599999999999">
            </div>
            <button id="carregar-carrinho"
                class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded-lg text-lg w-full md:w-auto">
                Carregar Carrinho
            </button>
        </div>
        <div class="flex flex-col items-center md:flex-row md:justify-center gap-4">
            <button id="start-button" class="bg-green-500 text-white px-6 py-2 rounded-lg text-lg w-full md:w-auto">
                Iniciar Câmera
            </button>
            <div id="qr-reader" class="w-full max-w-sm md:max-w-md h-72 mt-4 hidden mx-auto"></div>
            <button id="stop-button"
                class="mt-4 bg-red-500 text-white px-6 py-2 rounded-lg text-lg hidden w-full md:w-auto">
                Parar Câmera
            </button>
        </div>
        <div class="mt-8 border rounded-lg p-4">
            <h2 class="text-xl font-bold mb-4">Carrinho de Compras</h2>
            <div id="carrinho-vazio" class="text-center py-4 text-gray-500">
                Nenhum produto adicionado ao carrinho
            </div>
            <div id="carrinho-itens" class="hidden">
                <div id="lista-produtos-mobile" class="md:hidden space-y-3"></div>
                <div class="hidden md:block overflow-x-auto">
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
                        <tbody id="lista-produtos" class="bg-white divide-y divide-gray-200">
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-right font-semibold">Total do Carrinho:</td>
                                <td class="px-4 py-4 font-bold" id="total-carrinho">R$ 0,00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="mt-4 flex justify-end">
                    <button id="finalizar-venda"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg">
                        Finalizar Venda
                    </button>
                </div>
            </div>
        </div>
        <h2 class="text-xl md:text-2xl font-bold mt-8 mb-4 text-center">Últimos Produtos Vendidos</h2>
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
                            <td class="py-3 px-4 md:px-6 text-gray-600">{{$venda->produto->nome_produto}}</td>
                            <td class="py-3 px-4 md:px-6 text-gray-600">R$ {{number_format($venda->preco_venda, 2, ',', '.')}}
                            </td>
                            <td class="py-3 px-4 md:px-6 text-gray-600">{{$venda->produto->cod_produto}}</td>
                            <td class="py-3 px-4 md:px-6 text-gray-600">{{$venda->quantidade}}</td>
                            <td class="py-3 px-4 md:px-6 text-gray-600">{{$venda->usuario->name}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-6 flex justify-center">
                <nav class="flex items-center space-x-2">
                    <a href="{{$vendas->previousPageUrl()}}"
                        class="py-2 px-3 bg-gray-100 border rounded-l-lg hover:bg-gray-200 text-gray-600">
                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </a>
                    <span class="py-2 px-3 bg-gray-100 text-gray-600">{{$vendas->currentPage()}}</span>
                    <a href="{{$vendas->nextPageUrl()}}"
                        class="py-2 px-3 bg-gray-100 border rounded-r-lg hover:bg-gray-200 text-gray-600">
                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </a>
                </nav>
            </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js"></script>
        <script>
            (function () {
                'use strict';
                const CSRF_TOKEN = "{{ csrf_token() }}";
                const Utils = {
                    currency(v) {
                        const n = parseFloat(v || 0);
                        return 'R$ ' + n.toFixed(2).replace('.', ',');
                    },
                    getClient() {
                        return $('#client-input').val().trim();
                    },
                    hasClient() {
                        return this.getClient().length > 0;
                    },
                    async askClientIfEmpty() {
                        let c = this.getClient();
                        if (!c) {
                            c = window.prompt('Informe o telefone do cliente (WhatsApp):', '');
                            if (c) {
                                $('#client-input').val(c.trim());
                            }
                        }
                        return $('#client-input').val().trim() || null;
                    }
                };

                const QrScanner = {
                    scanner: null,
                    startButton: $('#start-button'),
                    stopButton: $('#stop-button'),
                    qrReader: $('#qr-reader'),

                    init() {
                        this.startButton.on('click', () => this.start());
                        this.stopButton.on('click', () => this.stop());
                    },

                    start() {
                        if (this.scanner) {
                            this.stop().then(() => this._startImpl());
                        } else {
                            this._startImpl();
                        }
                    },

                    _startImpl() {
                        this.scanner = new Html5Qrcode("qr-reader");
                        const config = { fps: 10, qrbox: { width: 300, height: 300 } };

                        this.scanner.start(
                            { facingMode: "environment" },
                            config,
                            this.handleScanSuccess,
                            this.handleScanError
                        ).catch(error => console.error("Erro ao iniciar scanner:", error));

                        this.toggleUI(true);
                    },

                    stop() {
                        if (this.scanner) {
                            return this.scanner.stop().then(() => {
                                this.toggleUI(false);
                                this.scanner = null;
                            }).catch(console.error);
                        }
                        return Promise.resolve();
                    },

                    toggleUI(starting) {
                        this.qrReader.toggleClass('hidden', !starting);
                        this.startButton.toggleClass('hidden', starting);
                        this.stopButton.toggleClass('hidden', !starting);
                    },

                    handleScanSuccess: (decodedText) => {
                        CartManager.addProductByQr(decodedText);
                        QrScanner.stop();
                    },

                    handleScanError: (errorMessage) => {
                        console.warn("Erro no scanner:", errorMessage);
                    }
                };

                const CartManager = {
                    state: {
                        cart: null,    
                        staged: [] 
                    },

                    dom: {
                        listDesktop: $('#lista-produtos'),
                        listMobile: $('#lista-produtos-mobile'),
                        empty: $('#carrinho-vazio'),
                        itemsWrapper: $('#carrinho-itens'),
                        total: $('#total-carrinho'),
                        btnFinalizar: $('#finalizar-venda'),
                    },

                    init() {
                        $('#carregar-carrinho').on('click', () => this.loadCartMergeStaged());
                        this.dom.listDesktop.on('click', '.quantity-btn', this.handleQuantityChange.bind(this));
                        this.dom.listDesktop.on('click', '.remover-btn', this.handleRemoveItem.bind(this));
                        this.dom.listMobile.on('click', '.quantity-btn', this.handleQuantityChange.bind(this));
                        this.dom.listMobile.on('click', '.remover-btn', this.handleRemoveItem.bind(this));
                        this.dom.btnFinalizar.on('click', () => this.finalizeSale());
                    },

                    async loadCart() {
                        const client = Utils.getClient();
                        if (!client) {
                            toastr.warning('Informe o cliente para carregar o carrinho.', 'Aviso');
                            return null;
                        }
                        try {
                            const res = await $.ajax({
                                url: "/verdurao/vendas/carrinho",
                                method: "POST",
                                data: { client, _token: CSRF_TOKEN }
                            });
                            if (res.success && res.cart) {
                                this.state.cart = res.cart;
                                this.render();
                                toastr.success('Carrinho carregado.', 'OK');
                                return res.cart;
                            }
                        } catch (e) {
                            toastr.error('Não foi possível carregar o carrinho.', 'Erro');
                            console.error(e);
                        }
                        return null;
                    },

                    async loadCartMergeStaged() {
                        const client = Utils.getClient() || await Utils.askClientIfEmpty();
                        if (!client) return;

                        const cart = await this.loadCart();
                        if (!cart) return;

                        if (this.state.staged.length) {
                            for (const it of this.state.staged) {
                                await this._addToBackend(client, it.id_produto, it.quantidade);
                            }
                            this.state.staged = [];
                            await this.loadCart(); 
                        }
                    },

                    async addProductByQr(qrCode) {
                        try {
                            const produtoResp = await $.ajax({
                                url: "/verdurao/vendas/buscar-produto",
                                method: "POST",
                                data: { codigo_qr: qrCode, _token: CSRF_TOKEN }
                            });
                            if (!produtoResp.success || !produtoResp.produto) {
                                toastr.error('Produto não encontrado.', 'Erro');
                                return;
                            }
                            const p = produtoResp.produto;

                            if (Utils.hasClient()) {
                                const client = Utils.getClient();
                                const addResp = await this._addToBackend(client, p.id_produto, 1);
                                if (addResp && addResp.cart) {
                                    this.state.cart = addResp.cart;
                                    toastr.success('Produto adicionado ao carrinho!', 'Sucesso');
                                    this.render();
                                }
                            } else {
                                const existing = this.state.staged.find(x => x.id_produto === p.id_produto);
                                if (existing) {
                                    existing.quantidade += 1;
                                } else {
                                    this.state.staged.push({
                                        _localId: Date.now() + Math.random(), // id local
                                        id_produto: p.id_produto,
                                        nome: p.nome_produto,
                                        preco: parseFloat(p.preco_venda || 0),
                                        quantidade: 1,
                                        cod_produto: p.cod_produto,
                                        unidade_medida: p.unidade_medida,
                                        estoque_disponivel: p.estoque_atual
                                    });
                                }
                                toastr.info('Produto adicionado (pendente de cliente).', 'Info');
                                this.render();
                            }
                        } catch (e) {
                            toastr.error('Erro ao adicionar item.', 'Erro');
                            console.error(e);
                        }
                    },

                    _addToBackend(client, id_produto, quantidade) {
                        return $.ajax({
                            url: "/verdurao/vendas/carrinho/adicionar",
                            method: "POST",
                            data: { client, id_produto, quantidade, _token: CSRF_TOKEN }
                        });
                    },

                    handleQuantityChange(event) {
                        const $btn = $(event.currentTarget);
                        const action = $btn.data('action');

                        const localId = $btn.data('lkey');  // para staged
                        const itemId = $btn.data('itemid'); // para backend
                        const delta = action === 'increment' ? 1 : -1;

                        if (itemId) {
                            this.updateQuantityBackend(itemId, delta);
                        } else if (localId) {
                            this.updateQuantityStaged(localId, delta);
                        }
                    },

                    async updateQuantityBackend(cartItemId, delta) {
                        if (!this.state.cart) return;
                        const item = (this.state.cart.items || []).find(i => i.id === cartItemId);
                        if (!item) return;
                        const nova = Math.max(0, parseInt(item.quantidade, 10) + delta);

                        try {
                            const res = await $.ajax({
                                url: "/verdurao/vendas/carrinho/quantidade",
                                method: "POST",
                                data: { client: Utils.getClient(), cart_item_id: cartItemId, quantidade: nova, _token: CSRF_TOKEN }
                            });
                            if (res.success && res.cart) {
                                this.state.cart = res.cart;
                                this.render();
                                toastr.success('Quantidade atualizada.', 'Sucesso');
                            } else {
                                toastr.error(res.message || 'Falha ao atualizar quantidade.', 'Erro');
                            }
                        } catch (e) {
                            toastr.error('Erro ao atualizar quantidade.', 'Erro');
                            console.error(e);
                        }
                    },

                    updateQuantityStaged(localId, delta) {
                        const it = this.state.staged.find(x => x._localId === localId);
                        if (!it) return;
                        const nova = it.quantidade + delta;
                        if (nova <= 0) {
                            this.state.staged = this.state.staged.filter(x => x._localId !== localId);
                        } else {
                            it.quantidade = nova;
                        }
                        this.render();
                    },

                    handleRemoveItem(event) {
                        const $btn = $(event.currentTarget);
                        const localId = $btn.data('lkey');
                        const itemId = $btn.data('itemid');

                        if (itemId) {
                            this.removeBackend(itemId);
                        } else if (localId) {
                            this.state.staged = this.state.staged.filter(x => x._localId !== localId);
                            this.render();
                            toastr.info('Produto removido.', 'Info');
                        }
                    },

                    async removeBackend(cartItemId) {
                        try {
                            const res = await $.ajax({
                                url: "/verdurao/vendas/carrinho/remover",
                                method: "POST",
                                data: { client: Utils.getClient(), cart_item_id: cartItemId, _token: CSRF_TOKEN }
                            });
                            if (res.success && res.cart) {
                                this.state.cart = res.cart;
                                this.render();
                                toastr.info('Produto removido.', 'Info');
                            } else {
                                toastr.error(res.message || 'Falha ao remover item.', 'Erro');
                            }
                        } catch (e) {
                            toastr.error('Erro ao remover item.', 'Erro');
                            console.error(e);
                        }
                    },

                    async finalizeSale() {
                        const client = Utils.getClient() || await Utils.askClientIfEmpty();
                        if (!client) return;

                        const cart = await this.loadCart();
                        if (!cart) return;

                        if (this.state.staged.length) {
                            for (const it of this.state.staged) {
                                await this._addToBackend(client, it.id_produto, it.quantidade);
                            }
                            this.state.staged = [];
                        }
                        const cart2 = await this.loadCart();
                        if (!cart2) return;

                        try {
                            const res = await $.ajax({
                                url: "/verdurao/vendas/registrar-venda",
                                method: "POST",
                                data: { client, _token: CSRF_TOKEN }
                            });
                            if (res.success) {
                                toastr.success('Venda registrada com sucesso!', 'Sucesso');
                                this.state.cart = null;
                                this.state.staged = [];
                                this.render();
                            } else {
                                const detalhes = (res.detalhes || [])
                                    .map(d => `Produto ${d.id_produto}: solicitado ${d.quantidade_solicitada ?? '-'} / disponível ${d.estoque_atual ?? '-'}`)
                                    .join('<br>');
                                toastr.error((res.message || 'Falha ao finalizar') + (detalhes ? '<br>' + detalhes : ''), 'Erro');
                            }
                        } catch (e) {
                            if (e.responseJSON) {
                                const r = e.responseJSON;
                                const detalhes = (r.detalhes || r.produtos_sem_estoque || [])
                                    .map(d => `Produto ${d.id_produto}: solicitado ${d.quantidade_solicitada ?? '-'} / disponível ${d.estoque_atual ?? '-'}`)
                                    .join('<br>');
                                toastr.error((r.message || 'Erro ao processar a venda.') + (detalhes ? '<br>' + detalhes : ''), 'Erro');
                            } else {
                                toastr.error('Erro ao processar a venda.', 'Erro');
                            }
                            console.error(e);
                        }
                    },
                    render() {
                        const hasBackendCart = !!this.state.cart;
                        const items = hasBackendCart ? (this.state.cart.items || []) : this.state.staged;

                        const hasItems = items.length > 0;
                        this.dom.empty.toggleClass('hidden', hasItems);
                        this.dom.itemsWrapper.toggleClass('hidden', !hasItems);
                        const rowsDesktop = items.map(it => {
                            const isLocal = !hasBackendCart;
                            const nome = isLocal ? it.nome : it.nome_produto;
                            const preco = isLocal ? it.preco : parseFloat(it.preco_unit || 0);
                            const qtd = isLocal ? it.quantidade : it.quantidade;
                            const totalLinha = isLocal ? (it.preco * it.quantidade) : parseFloat(it.subtotal_valor || 0);
                            const attrId = isLocal ? `data-lkey="${it._localId}"` : `data-itemid="${it.id}"`;

                            return `
                                <tr>
                                    <td class="px-4 py-3">${nome}</td>
                                    <td class="px-4 py-3">${Utils.currency(preco)}</td>
                                    <td class="px-4 py-3">
                                        <div class="inline-flex items-center border rounded-md overflow-hidden">
                                            <button class="quantity-btn px-3 py-1" data-action="decrement" ${attrId}>-</button>
                                            <span class="px-4 py-1 select-none">${qtd}</span>
                                            <button class="quantity-btn px-3 py-1" data-action="increment" ${attrId}>+</button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">${Utils.currency(totalLinha)}</td>
                                    <td class="px-4 py-3">
                                        <button class="remover-btn text-red-500 hover:text-red-700" ${attrId}>Remover</button>
                                    </td>
                                </tr>
                            `;
                        }).join('');
                        this.dom.listDesktop.html(rowsDesktop);
                        const rowsMobile = items.map(it => {
                            const isLocal = !hasBackendCart;
                            const nome = isLocal ? it.nome : it.nome_produto;
                            const preco = isLocal ? it.preco : parseFloat(it.preco_unit || 0);
                            const qtd = isLocal ? it.quantidade : it.quantidade;
                            const totalLinha = isLocal ? (it.preco * it.quantidade) : parseFloat(it.subtotal_valor || 0);
                            const attrId = isLocal ? `data-lkey="${it._localId}"` : `data-itemid="${it.id}"`;

                            return `
                                <div class="border rounded-lg p-3">
                                    <div class="flex justify-between">
                                        <div class="font-medium">${nome}</div>
                                        <div class="font-semibold">${Utils.currency(totalLinha)}</div>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-600">Preço: ${Utils.currency(preco)}</div>
                                    <div class="mt-3 flex items-center justify-between">
                                        <div class="inline-flex items-center border rounded-md overflow-hidden">
                                            <button class="quantity-btn px-3 py-2" data-action="decrement" ${attrId}>-</button>
                                            <span class="px-4 py-2 select-none">${qtd}</span>
                                            <button class="quantity-btn px-3 py-2" data-action="increment" ${attrId}>+</button>
                                        </div>
                                        <button class="remover-btn text-red-500 hover:text-red-700" ${attrId}>Remover</button>
                                    </div>
                                </div>
                            `;
                        }).join('');
                        this.dom.listMobile.html(rowsMobile);

                        // Total
                        if (hasBackendCart) {
                            this.dom.total.text(Utils.currency(this.state.cart.total_valor || 0));
                        } else {
                            const totalLocal = this.state.staged.reduce((acc, it) => acc + (it.preco * it.quantidade), 0);
                            this.dom.total.text(Utils.currency(totalLocal));
                        }
                    }
                };

                // Inicialização
                $(document).ready(() => {
                    QrScanner.init();
                    CartManager.init();
                });
            })();
        </script>
    </div>
@endsection