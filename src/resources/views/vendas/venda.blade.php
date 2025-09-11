@extends('layouts.principal')

@section('conteudo')
    <div class="bg-white p-6 rounded-md shadow-md w-full max-w-7xl mx-auto">
        <h1 class="text-2xl md:text-3xl font-bold mb-6 text-center">Registrar Venda</h1>

        {{-- Cliente (telefone WhatsApp) --}}
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
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Produto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantidade</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody id="lista-produtos" class="bg-white divide-y divide-gray-200">
                        {{-- linhas via JS --}}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-semibold">Total do Carrinho:</td>
                            <td class="px-6 py-4 font-bold" id="total-carrinho">R$ 0,00</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

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

            {{-- Paginação --}}
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
                    ensureClient() {
                        const c = $('#client-input').val().trim();
                        if (!c) {
                            toastr.warning('Informe o cliente (telefone WhatsApp).', 'Aviso');
                            return null;
                        }
                        return c;
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
                        const client = Utils.ensureClient();
                        if (!client) return;

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
                    dom: {
                        list: $('#lista-produtos'),
                        empty: $('#carrinho-vazio'),
                        items: $('#carrinho-itens'),
                        total: $('#total-carrinho'),
                        btnFinalizar: $('#finalizar-venda'),
                    },

                    init() {
                        // carrega carrinho pelo botão
                        $('#carregar-carrinho').on('click', () => this.loadCart());

                        // ações na lista
                        this.dom.list.on('click', '.quantity-btn', this.handleQuantityChange.bind(this));
                        this.dom.list.on('click', '.remover-btn', this.handleRemoveItem.bind(this));

                        // finalizar
                        this.dom.btnFinalizar.on('click', () => this.finalizeSale());
                    },

                    async loadCart() {
                        const client = Utils.ensureClient();
                        if (!client) return;

                        try {
                            const res = await $.ajax({
                                url: "/verdurao/vendas/carrinho",
                                method: "POST",
                                data: { client, _token: CSRF_TOKEN }
                            });

                            if (res.success && res.cart) {
                                this.renderCart(res.cart);
                                toastr.success('Carrinho carregado.', 'OK');
                            }
                        } catch (e) {
                            toastr.error('Não foi possível carregar o carrinho.', 'Erro');
                            console.error(e);
                        }
                    },

                    async addProductByQr(qrCode) {
                        const client = Utils.ensureClient();
                        if (!client) return;

                        try {
                            // 1) Buscar produto pelo QR
                            const produtoResp = await $.ajax({
                                url: "/verdurao/vendas/buscar-produto",
                                method: "POST",
                                data: { codigo_qr: qrCode, _token: CSRF_TOKEN }
                            });

                            if (!produtoResp.success || !produtoResp.produto) {
                                toastr.error('Produto não encontrado.', 'Erro');
                                return;
                            }

                            const produto = produtoResp.produto;

                            // 2) Adicionar 1 unidade ao carrinho no backend
                            const addResp = await $.ajax({
                                url: "/verdurao/vendas/carrinho/adicionar",
                                method: "POST",
                                data: {
                                    client,
                                    id_produto: produto.id_produto,
                                    quantidade: 1,
                                    _token: CSRF_TOKEN
                                }
                            });

                            if (addResp.success && addResp.cart) {
                                this.renderCart(addResp.cart);
                                toastr.success('Produto adicionado ao carrinho!', 'Sucesso');
                            } else {
                                toastr.error(addResp.message || 'Falha ao adicionar item.', 'Erro');
                            }
                        } catch (e) {
                            toastr.error('Erro ao adicionar item.', 'Erro');
                            console.error(e);
                        }
                    },

                    handleQuantityChange(event) {
                        const $btn = $(event.currentTarget);
                        const itemId = $btn.data('itemid');
                        const action = $btn.data('action');

                        const $row = $btn.closest('tr');
                        const currentQty = parseInt($row.find('.quantidade').text(), 10) || 0;
                        const nextQty = action === 'increment' ? currentQty + 1 : currentQty - 1;

                        this.updateQuantity(itemId, nextQty);
                    },

                    async updateQuantity(cartItemId, quantidade) {
                        const client = Utils.ensureClient();
                        if (!client) return;

                        try {
                            const res = await $.ajax({
                                url: "/verdurao/vendas/carrinho/quantidade",
                                method: "POST",
                                data: {
                                    client,
                                    cart_item_id: cartItemId,
                                    quantidade,
                                    _token: CSRF_TOKEN
                                }
                            });

                            if (res.success && res.cart) {
                                this.renderCart(res.cart);
                                toastr.success('Quantidade atualizada.', 'Sucesso');
                            } else {
                                toastr.error(res.message || 'Falha ao atualizar quantidade.', 'Erro');
                            }
                        } catch (e) {
                            toastr.error('Erro ao atualizar quantidade.', 'Erro');
                            console.error(e);
                        }
                    },

                    handleRemoveItem(event) {
                        const itemId = $(event.currentTarget).data('itemid');
                        this.removeItem(itemId);
                    },

                    async removeItem(cartItemId) {
                        const client = Utils.ensureClient();
                        if (!client) return;

                        try {
                            const res = await $.ajax({
                                url: "/verdurao/vendas/carrinho/remover",
                                method: "POST",
                                data: {
                                    client,
                                    cart_item_id: cartItemId,
                                    _token: CSRF_TOKEN
                                }
                            });

                            if (res.success && res.cart) {
                                this.renderCart(res.cart);
                                toastr.info('Produto removido do carrinho.', 'Info');
                            } else {
                                toastr.error(res.message || 'Falha ao remover item.', 'Erro');
                            }
                        } catch (e) {
                            toastr.error('Erro ao remover item.', 'Erro');
                            console.error(e);
                        }
                    },

                    // Finalizar venda -> cria order, baixa estoque (service faz verificação interna)
                    async finalizeSale() {
                        const client = Utils.ensureClient();
                        if (!client) return;

                        try {
                            const res = await $.ajax({
                                url: "/verdurao/vendas/registrar-venda",
                                method: "POST",
                                data: { client, _token: CSRF_TOKEN }
                            });

                            if (res.success) {
                                toastr.success('Venda registrada com sucesso!', 'Sucesso');
                                // recarrega carrinho vazio e a lista de vendas (página)
                                await this.loadCart();
                                location.reload();
                            } else {
                                // quando service retorna 400 com faltantes
                                const detalhes = (res.detalhes || [])
                                    .map(d => `Produto ${d.id_produto}: solicitado ${d.quantidade_solicitada ?? '-'} / disponível ${d.estoque_atual ?? '-'}`)
                                    .join('<br>');
                                toastr.error((res.message || 'Falha ao finalizar') + (detalhes ? '<br>' + detalhes : ''), 'Erro');
                            }
                        } catch (e) {
                            // pode vir 400 com json
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

                    renderCart(cart) {
                        const items = (cart.items || []);
                        const hasItems = items.length > 0;

                        this.dom.empty.toggleClass('hidden', hasItems);
                        this.dom.items.toggleClass('hidden', !hasItems);

                        if (!hasItems) {
                            this.dom.list.html('');
                            this.dom.total.text(Utils.currency(0));
                            return;
                        }

                        const rows = items.map(item => {
                            const totalLinha = parseFloat(item.subtotal_valor || 0);
                            const preco = parseFloat(item.preco_unit || 0);
                            return `
                                        <tr data-itemid="${item.id}">
                                            <td class="px-6 py-4 whitespace-nowrap">${item.nome_produto}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">${Utils.currency(preco)}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <button class="quantity-btn bg-gray-200 px-2 py-1 rounded-l"
                                                        data-action="decrement" data-itemid="${item.id}">-</button>
                                                    <span class="quantidade bg-gray-100 px-4 py-1">${item.quantidade}</span>
                                                    <button class="quantity-btn bg-gray-200 px-2 py-1 rounded-r"
                                                        data-action="increment" data-itemid="${item.id}">+</button>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">${Utils.currency(totalLinha)}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <button class="remover-btn text-red-500 hover:text-red-700"
                                                    data-itemid="${item.id}">Remover</button>
                                            </td>
                                        </tr>
                                    `;
                        }).join('');

                        this.dom.list.html(rows);
                        this.dom.total.text(Utils.currency(cart.total_valor || 0));
                    }
                };

                // Inicialização
                $(document).ready(() => {
                    QrScanner.init();
                    CartManager.init();
                });
            })();
        </script>
@endsection