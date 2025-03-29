@extends('layouts.principal')

@section('conteudo')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold text-center">Histórico de Vendas</h1>

    <div class="mt-6 bg-white p-4 rounded shadow-lg">
        <table class="w-full border-collapse border border-gray-300 text-center">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 p-3">Produto</th>
                    <th class="border border-gray-300 p-3">Valor</th>
                    <th class="border border-gray-300 p-3">Quantidade</th>
                    <th class="border border-gray-300 p-3">Data</th>
                </tr>
            </thead>
            <tbody id="tabela-vendas">
                <!-- As vendas serão carregadas aqui via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<script>
    function atualizarVendas() {
        $.ajax({
            url: "/vendas/listar",
            method: "GET",
            success: function(response) {
                $("#tabela-vendas").html('');
                response.vendas.forEach(venda => {
                    $("#tabela-vendas").append(`
                        <tr>
                            <td class="border border-gray-300 p-2">${venda.produto.nome}</td>
                            <td class="border border-gray-300 p-2">R$ ${parseFloat(venda.valor_venda).toFixed(2)}</td>
                            <td class="border border-gray-300 p-2">${venda.quantidade}</td>
                            <td class="border border-gray-300 p-2">${new Date(venda.created_at).toLocaleString()}</td>
                        </tr>
                    `);
                });
            },
            error: function() {
                console.log("Erro ao carregar vendas.");
            }
        });
    }

    setInterval(atualizarVendas, 5000); // Atualiza a cada 5 segundos
    atualizarVendas(); // Chama a função na inicialização
</script>
@endsection
