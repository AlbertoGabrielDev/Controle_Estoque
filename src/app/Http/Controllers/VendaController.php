<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Services\VendaService;
use Illuminate\Http\Request;

class VendaController extends Controller
{
    public function __construct(private VendaService $service)
    {
        //
    }

    /* ===========================
       LISTAGEM (mantida)
       =========================== */
    public function vendas()
    {
        $vendas = Venda::with('usuario')->paginate(10);
        return view('vendas.venda', compact('vendas'));
    }

    /* ===========================
       BUSCAR PRODUTO POR QR CODE
       =========================== */
    public function buscarProduto(Request $request)
    {
        $request->validate(['codigo_qr' => 'required|string']);

        try {
            $dados = $this->service->buscarProdutoPorQr($request->codigo_qr);

            return response()->json([
                'success' => true,
                'produto' => $dados
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado!'
            ], 404);
        }
    }

    /* ===========================
       VERIFICAÇÃO DE ESTOQUE
       =========================== */
    public function verificarEstoque(Request $request)
    {
        $request->validate([
            'itens'                 => 'required|array|min:1',
            'itens.*.id_produto'    => 'required|integer',
            'itens.*.quantidade'    => 'required|integer|min:1',
        ]);

        try {
            $faltantes = $this->service->verificarEstoqueItens($request->itens);

            if (!empty($faltantes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alguns produtos não possuem estoque suficiente',
                    'produtos_sem_estoque' => $faltantes
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Estoque disponível para todos os itens'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar estoque: '.$e->getMessage()
            ], 500);
        }
    }

    /* ===========================
       CARRINHO
       =========================== */

    // Criar/obter carrinho aberto do cliente
    public function carrinho(Request $request)
    {
        $request->validate(['client' => 'required|string|max:20']);
        $cart = $this->service->obterCarrinho($request->client);

        return response()->json([
            'success' => true,
            'cart'    => $cart->toArray(),
        ]);
    }

    // Adicionar item ao carrinho
    public function adicionarItem(Request $request)
    {
        $request->validate([
            'client'      => 'required|string|max:20',
            'id_produto'  => 'required|integer',
            'quantidade'  => 'required|integer|min:1',
        ]);

        try {
            $cart = $this->service->adicionarItem(
                $request->client,
                (int) $request->id_produto,
                (int) $request->quantidade
            );

            return response()->json([
                'success' => true,
                'cart'    => $cart->toArray(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao adicionar item: '.$e->getMessage(),
            ], 400);
        }
    }

    // Atualizar quantidade de um item
    public function atualizarQuantidade(Request $request)
    {
        $request->validate([
            'client'       => 'required|string|max:20',
            'cart_item_id' => 'required|integer',
            'quantidade'   => 'required|integer|min:0',
        ]);

        try {
            $cart = $this->service->atualizarQuantidadeItem(
                $request->client,
                (int) $request->cart_item_id,
                (int) $request->quantidade
            );

            return response()->json([
                'success' => true,
                'cart'    => $cart->toArray(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar item: '.$e->getMessage(),
            ], 400);
        }
    }

    // Remover item do carrinho
    public function removerItem(Request $request)
    {
        $request->validate([
            'client'       => 'required|string|max:20',
            'cart_item_id' => 'required|integer',
        ]);

        try {
            $cart = $this->service->removerItem(
                $request->client,
                (int) $request->cart_item_id
            );

            return response()->json([
                'success' => true,
                'cart'    => $cart->toArray(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover item: '.$e->getMessage(),
            ], 400);
        }
    }

    /* ===========================
       FINALIZAR VENDA (CHECKOUT)
       =========================== */
    public function registrarVenda(Request $request)
    {
        $request->validate([
            'client' => 'required|string|max:20',
        ]);

        try {
            $resultado = $this->service->finalizarVendaDoCarrinho($request->client);

            if (!$resultado['ok']) {
                return response()->json([
                    'success'  => false,
                    'message'  => $resultado['mensagem'] ?? 'Falha ao finalizar',
                    'detalhes' => $resultado['faltantes'] ?? [],
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Venda registrada com sucesso!',
                'order'   => $resultado,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar a venda: '.$e->getMessage(),
            ], 500);
        }
    }

    public function historicoVendas()
    {
        // A view pode consumir a tabela orders / order_items
        return view('vendas.historico_vendas');
    }
}
