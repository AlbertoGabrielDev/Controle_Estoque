@extends('layouts.principal')

@section('conteudo')
<div class="bg-white p-6 rounded-md shadow-md w-full max-w-7xl mx-auto">
    <h1 class="text-2xl md:text-3xl font-bold mb-6 text-center">Registrar Venda</h1>

    <div class="flex flex-col items-center md:flex-row md:justify-center gap-4">
        <button id="start-button" class="bg-green-500 text-white px-6 py-2 rounded-lg text-lg w-full md:w-auto">
            Iniciar Câmera
        </button>

        <div id="qr-reader" class="w-full max-w-sm md:max-w-md h-72 mt-4 hidden mx-auto"></div>

        <button id="stop-button" class="mt-4 bg-red-500 text-white px-6 py-2 rounded-lg text-lg hidden w-full md:w-auto">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody id="lista-produtos" class="bg-white divide-y divide-gray-200">
                    <!-- Itens serão adicionados aqui via JS -->
                </tbody>
            </table>

            <div class="mt-4 flex justify-end">
                <button id="finalizar-venda" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg">
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
                    <td class="py-3 px-4 md:px-6 text-gray-600">R$ {{number_format($venda->preco_venda, 2, ',', '.')}}</td>
                    <td class="py-3 px-4 md:px-6 text-gray-600">{{$venda->produto->cod_produto}}</td>
                    <td class="py-3 px-4 md:px-6 text-gray-600">{{$venda->quantidade}}</td>
                    <td class="py-3 px-4 md:px-6 text-gray-600">{{$venda->usuario->name}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <!-- Paginação -->
        <div class="mt-6 flex justify-center">
            <nav class="flex items-center space-x-2">
                <a href="{{$vendas->previousPageUrl()}}" class="py-2 px-3 bg-gray-100 border rounded-l-lg hover:bg-gray-200 text-gray-600">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </a>
                <span class="py-2 px-3 bg-gray-100 text-gray-600">{{$vendas->currentPage()}}</span>
                <a href="{{$vendas->nextPageUrl()}}" class="py-2 px-3 bg-gray-100 border rounded-r-lg hover:bg-gray-200 text-gray-600">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </a>
            </nav>
        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js"></script>
    <script>
        (function() {
            'use strict';

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
                    this.scanner = new Html5Qrcode("qr-reader");
                    const config = {
                        fps: 10,
                        qrbox: {
                            width: 300,
                            height: 300
                        }
                    };

                    this.scanner.start({
                            facingMode: "environment"
                        },
                        config,
                        this.handleScanSuccess,
                        this.handleScanError
                    ).catch(error => console.error("Erro ao iniciar scanner:", error));

                    this.toggleUI(true);
                },

                stop() {
                    if (this.scanner) {
                        this.scanner.stop().then(() => this.toggleUI(false)).catch(console.error);
                    }
                },

                toggleUI(starting) {
                    this.qrReader.toggleClass('hidden', !starting);
                    this.startButton.toggleClass('hidden', starting);
                    this.stopButton.toggleClass('hidden', !starting);
                },

                handleScanSuccess: (decodedText) => {
                    CartManager.addProduct(decodedText);
                    QrScanner.stop();
                },

                handleScanError: (errorMessage) => {
                    console.warn("Erro no scanner:", errorMessage);
                }
            };

            const CartManager = {
                cart: [],
                productsData: {},
                domElements: {
                    list: $('#lista-produtos'),
                    empty: $('#carrinho-vazio'),
                    items: $('#carrinho-itens')
                },

                init() {
                    this.domElements.list.on('click', '.quantity-btn', this.handleQuantityChange.bind(this));
                    this.domElements.list.on('click', '.remover-btn', this.handleRemoveItem.bind(this));
                },

                async addProduct(qrCode) {
                    if (this.productsData[qrCode]) {
                        this.updateQuantity(qrCode, 1);
                        return;
                    }

                    try {
                        const response = await this.fetchProductData(qrCode);
                        if (response.success) {
                            this.productsData[qrCode] = response.produto;
                            this.addToCart(response.produto);
                            toastr.success("Produto adicionado ao carrinho!", "Sucesso");
                        }
                    } catch (error) {
                        toastr.error("Erro ao buscar produto!", "Erro");
                        console.error("Erro:", error);
                    }
                },

                async fetchProductData(qrCode) {
                    return $.ajax({
                        url: "/verdurao/vendas/buscar-produto",
                        method: "POST",
                        data: {
                            codigo_qr: qrCode,
                            _token: "{{ csrf_token() }}"
                        }
                    });
                },

                addToCart(produto) {
                    const existingItem = this.cart.find(item => item.codigo_qr === produto.qrcode);

                    if (existingItem) {
                        this.updateQuantity(produto.qrcode, 1);
                        return;
                    }

                    this.cart.push({
                        codigo_qr: produto.qrcode,
                        id_produto: produto.id_produto,
                        nome: produto.nome_produto,
                        preco: produto.preco_venda,
                        quantidade: 1,
                        cod_produto: produto.cod_produto,
                        unidade_medida: produto.unidade_medida,
                        estoque_disponivel: produto.estoque_atual
                    });

                    this.updateCartUI();
                },

                handleQuantityChange(event) {
                    const qrCode = $(event.target).data('qrcode');
                    const action = $(event.target).data('action');
                    this.updateQuantity(qrCode, action === 'increment' ? 1 : -1);
                },

                handleRemoveItem(event) {
                    const qrCode = $(event.target).data('qrcode');
                    this.removeItem(qrCode);
                },

                updateQuantity(qrCode, delta) {
                    const item = this.cart.find(item => item.codigo_qr === qrCode);
                    if (!item) return;

                    if (delta > 0 && item.quantidade >= item.estoque_disponivel) {
                        toastr.warning("Estoque insuficiente!", "Aviso");
                        return;
                    }

                    item.quantidade += delta;

                    if (item.quantidade < 1) {
                        this.removeItem(qrCode);
                    } else {
                        this.updateCartUI();
                        toastr.success("Quantidade atualizada!", "Sucesso");
                    }
                },

                removeItem(qrCode) {
                    this.cart = this.cart.filter(item => item.codigo_qr !== qrCode);
                    delete this.productsData[qrCode];
                    this.updateCartUI();
                    toastr.info("Produto removido do carrinho", "Info");
                },

                updateCartUI() {
                    this.domElements.empty.toggleClass('hidden', this.cart.length > 0);
                    this.domElements.items.toggleClass('hidden', this.cart.length === 0);

                    this.domElements.list.html(this.generateCartHTML());
                },

                generateCartHTML() {
                    return this.cart.map(item => `
                    <tr class="produto-item" data-qrcode="${item.codigo_qr}">
                        <td class="px-6 py-4 whitespace-nowrap">${item.nome}</td>
                        <td class="px-6 py-4 whitespace-nowrap">R$ ${item.preco.toFixed(2)}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <button class="quantity-btn bg-gray-200 px-2 py-1 rounded-l" 
                                    data-action="decrement" data-qrcode="${item.codigo_qr}">-</button>
                                <span class="quantidade bg-gray-100 px-4 py-1">${item.quantidade}</span>
                                <button class="quantity-btn bg-gray-200 px-2 py-1 rounded-r" 
                                    data-action="increment" data-qrcode="${item.codigo_qr}">+</button>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">R$ ${(item.preco * item.quantidade).toFixed(2)}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button class="remover-btn text-red-500 hover:text-red-700" 
                                data-qrcode="${item.codigo_qr}">Remover</button>
                        </td>
                    </tr>
                `).join('');
                },

                async finalizeSale() {
                    if (this.cart.length === 0) {
                        toastr.warning("Adicione produtos ao carrinho", "Aviso");
                        return;
                    }

                    try {
                        const stockCheck = await this.checkStock();
                        if (!stockCheck.success) throw new Error(stockCheck.message);

                        const saleResult = await this.registerSale();
                        if (saleResult.success) {
                            this.clearCart();
                            toastr.success("Venda registrada com sucesso!", "Sucesso");
                            location.reload();
                        }
                    } catch (error) {
                        toastr.error(error.message, "Erro");
                    }
                },

                async checkStock() {
                    return $.ajax({
                        url: "/verdurao/vendas/verificar-estoque",
                        method: "POST",
                        data: {
                            itens: this.cart,
                            _token: "{{ csrf_token() }}"
                        }
                    });
                },

                async registerSale() {
                    return $.ajax({
                        url: "/verdurao/vendas/registrar-venda",
                        method: "POST",
                        data: {
                            itens: this.cart,
                            _token: "{{ csrf_token() }}"
                        }
                    });
                },

                clearCart() {
                    this.cart = [];
                    this.productsData = {};
                    this.updateCartUI();
                }
            };

            // Inicialização
            $(document).ready(() => {
                QrScanner.init();
                CartManager.init();
                $('#finalizar-venda').on('click', () => CartManager.finalizeSale());
            });

        })();
    </script>
    @endsection