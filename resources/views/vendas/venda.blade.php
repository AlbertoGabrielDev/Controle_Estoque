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
        let html5QrCode;
        let carrinho = [];
        let produtosData = {}; // Armazenará dados dos produtos para referência

        function startQRCodeScanner() {
            html5QrCode = new Html5Qrcode("qr-reader");
            const config = {
                fps: 10,
                qrbox: {
                    width: 300,
                    height: 300
                }
            };

            html5QrCode.start({
                    facingMode: "environment"
                },
                config,
                (decodedText) => {
                    adicionarProdutoAoCarrinho(decodedText);
                    stopQRCodeScanner();
                },
                (errorMessage) => {
                    console.log(errorMessage);
                }
            ).catch((err) => {
                console.log("Erro ao iniciar o scanner: ", err);
            });

            $('#qr-reader').removeClass('hidden');
            $('#stop-button').removeClass('hidden');
            $('#start-button').addClass('hidden');
        }

        function stopQRCodeScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    $('#qr-reader').addClass('hidden');
                    $('#start-button').removeClass('hidden');
                    $('#stop-button').addClass('hidden');
                }).catch((err) => {
                    console.log("Erro ao parar a câmera: ", err);
                });
            }
        }

        function adicionarProdutoAoCarrinho(qrCode) {
            // Verifica se já temos os dados do produto
            if (produtosData[qrCode]) {
                incrementarQuantidade(qrCode);
                return;
            }

            // Busca os dados do produto
            $.ajax({
                url: "/verdurao/vendas/buscar-produto",
                method: "POST",
                data: {
                    codigo_qr: qrCode,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        produtosData[qrCode] = response.produto;
                        adicionarItemAoCarrinho(response.produto);
                        toastr.success("Produto adicionado ao carrinho!", "Sucesso");
                    } else {
                        toastr.error(response.message, "Erro");
                    }
                },
                error: function(xhr) {
                    toastr.error("Erro ao buscar produto!", "Erro");
                    console.error("Erro ao buscar produto:", xhr.responseText);
                }
            });
        }

        function adicionarItemAoCarrinho(produto) {
            // Verifica se o produto já está no carrinho
            const itemExistente = carrinho.find(item => item.codigo_qr === produto.qrcode);

            if (itemExistente) {
                incrementarQuantidade(produto.qrcode);
                return;
            }

            // Adiciona novo item ao carrinho
            carrinho.push({
                codigo_qr: produto.qrcode,
                id_produto: produto.id_produto,
                nome: produto.nome_produto,
                preco: produto.preco_venda,
                quantidade: 1,
                cod_produto: produto.cod_produto,
                unidade_medida: produto.unidade_medida,
                estoque_disponivel: produto.estoque_atual
            });

            atualizarCarrinho();
        }

        function incrementarQuantidade(qrCode) {
            const itemIndex = carrinho.findIndex(item => item.codigo_qr === qrCode);

            if (itemIndex !== -1) {
                // Verifica se há estoque disponível
                if (carrinho[itemIndex].quantidade < carrinho[itemIndex].estoque_disponivel) {
                    carrinho[itemIndex].quantidade++;
                    atualizarCarrinho();
                    toastr.success("Quantidade atualizada!", "Sucesso");
                } else {
                    toastr.warning("Quantidade máxima em estoque atingida!", "Aviso");
                }
            }
        }

        function decrementarQuantidade(qrCode) {
            const itemIndex = carrinho.findIndex(item => item.codigo_qr === qrCode);

            if (itemIndex !== -1) {
                if (carrinho[itemIndex].quantidade > 1) {
                    carrinho[itemIndex].quantidade--;
                } else {
                    removerItemDoCarrinho(qrCode);
                    return;
                }
                atualizarCarrinho();
            }
        }

        function removerItemDoCarrinho(qrCode) {
            carrinho = carrinho.filter(item => item.codigo_qr !== qrCode);
            atualizarCarrinho();
            toastr.info("Produto removido do carrinho", "Info");
        }

        function atualizarCarrinho() {
            const $listaProdutos = $('#lista-produtos');
            $listaProdutos.empty();

            if (carrinho.length === 0) {
                $('#carrinho-vazio').removeClass('hidden');
                $('#carrinho-itens').addClass('hidden');
                return;
            }

            $('#carrinho-vazio').addClass('hidden');
            $('#carrinho-itens').removeClass('hidden');

            carrinho.forEach(item => {
                const totalItem = (item.preco * item.quantidade).toFixed(2);

                $listaProdutos.append(`
                <tr class="produto-item" data-qrcode="${item.codigo_qr}">
                    <td class="px-6 py-4 whitespace-nowrap">${item.nome}</td>
                    <td class="px-6 py-4 whitespace-nowrap">R$ ${item.preco.toFixed(2)}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <button class="decrementar-btn bg-gray-200 px-2 py-1 rounded-l" data-qrcode="${item.codigo_qr}">-</button>
                            <span class="quantidade bg-gray-100 px-4 py-1">${item.quantidade}</span>
                            <button class="incrementar-btn bg-gray-200 px-2 py-1 rounded-r" data-qrcode="${item.codigo_qr}">+</button>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">R$ ${totalItem}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <button class="remover-btn text-red-500 hover:text-red-700" data-qrcode="${item.codigo_qr}">
                            Remover
                        </button>
                    </td>
                </tr>
            `);
            });

            // Atualiza eventos dos botões
            $('.incrementar-btn').on('click', function() {
                incrementarQuantidade($(this).data('qrcode'));
            });

            $('.decrementar-btn').on('click', function() {
                decrementarQuantidade($(this).data('qrcode'));
            });

            $('.remover-btn').on('click', function() {
                removerItemDoCarrinho($(this).data('qrcode'));
            });
        }

        function finalizarVenda() {
            if (carrinho.length === 0) {
                toastr.warning("Adicione produtos ao carrinho antes de finalizar", "Aviso");
                return;
            }

            // Verifica estoque antes de enviar
            $.ajax({
                url: "/verdurao/vendas/verificar-estoque",
                method: "POST",
                data: {
                    itens: carrinho,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        enviarVenda();
                    } else {
                        toastr.error(response.message, "Erro no estoque");
                        // Atualiza estoque disponível nos itens
                        response.produtos_sem_estoque.forEach(produto => {
                            const itemIndex = carrinho.findIndex(item => item.id_produto === produto.id_produto);
                            if (itemIndex !== -1) {
                                carrinho[itemIndex].estoque_disponivel = produto.estoque_atual;
                                if (carrinho[itemIndex].quantidade > produto.estoque_atual) {
                                    carrinho[itemIndex].quantidade = produto.estoque_atual;
                                    toastr.warning(`Ajustada quantidade de ${carrinho[itemIndex].nome} para o disponível em estoque`, "Ajuste Automático");
                                }
                            }
                        });
                        atualizarCarrinho();
                    }
                },
                error: function(xhr) {
                    toastr.error("Erro ao verificar estoque", "Erro");
                    console.error("Erro ao verificar estoque:", xhr.responseText);
                }
            });
        }

        function enviarVenda() {
            $.ajax({
                url: "/verdurao/vendas/registrar-venda",
                method: "POST",
                data: {
                    itens: carrinho,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success("Venda registrada com sucesso!", "Sucesso");
                        carrinho = [];
                        atualizarCarrinho();
                        // Atualiza o histórico de vendas (se necessário)
                        location.reload();
                    } else {
                        toastr.error(response.message, "Erro");
                    }
                },
                error: function(xhr) {
                    toastr.error("Erro ao registrar venda", "Erro");
                    console.error("Erro ao registrar venda:", xhr.responseText);
                }
            });
        }

        // Event Listeners
        $('#start-button').on('click', startQRCodeScanner);
        $('#stop-button').on('click', stopQRCodeScanner);
        $('#finalizar-venda').on('click', finalizarVenda);
    </script>
    @endsection