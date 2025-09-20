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

    public function vendas()
    {
        $vendas = Venda::with('usuario')->paginate(10);
        return view('vendas.venda', compact('vendas'));
    }

    public function buscarProduto(Request $request)
    {
        $request->validate([
            'codigo_qr' => 'nullable|string',
            'codigo_produto' => 'nullable|string',
        ]);

        if (!$request->filled('codigo_qr') && !$request->filled('codigo_produto')) {
            return response()->json([
                'success' => false,
                'message' => 'Informe codigo_qr ou codigo_produto.'
            ], 422);
        }

        try {
            $dados = $this->service->buscarProduto(
                $request->input('codigo_qr'),
                $request->input('codigo_produto')
            );

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

    public function verificarEstoque(Request $request)
    {
        $request->validate([
            'itens' => 'required|array|min:1',
            'itens.*.id_produto' => 'required|integer',
            'itens.*.quantidade' => 'required|integer|min:1',
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
                'message' => 'Erro ao verificar estoque: ' . $e->getMessage()
            ], 500);
        }
    }

    public function carrinho(Request $request)
    {
        $request->validate(['client' => 'required|string|max:20']);
        $cart = $this->service->obterCarrinho($request->client);

        return response()->json([
            'success' => true,
            'cart' => $cart->toArray(),
        ]);
    }

    public function adicionarItem(Request $request)
    {
        $request->validate([
            'client' => 'required|string|max:20',
            'id_produto' => 'required|integer',
            'quantidade' => 'required|integer|min:1',
        ]);

        try {
            $cart = $this->service->adicionarItem(
                $request->client,
                (int) $request->id_produto,
                (int) $request->quantidade
            );

            return response()->json([
                'success' => true,
                'cart' => $cart->toArray(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao adicionar item: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function atualizarQuantidade(Request $request)
    {
        $request->validate([
            'client' => 'required|string|max:20',
            'cart_item_id' => 'required|integer',
            'quantidade' => 'required|integer|min:0',
        ]);

        try {
            $cart = $this->service->atualizarQuantidadeItem(
                $request->client,
                (int) $request->cart_item_id,
                (int) $request->quantidade
            );

            return response()->json([
                'success' => true,
                'cart' => $cart->toArray(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar item: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function removerItem(Request $request)
    {
        $request->validate([
            'client' => 'required|string|max:20',
            'cart_item_id' => 'required|integer',
        ]);

        try {
            $cart = $this->service->removerItem(
                $request->client,
                (int) $request->cart_item_id
            );

            return response()->json([
                'success' => true,
                'cart' => $cart->toArray(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover item: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function registrarVenda(Request $request)
    {
        $request->validate([
            'client' => 'required|string|max:20',
        ]);

        try {
            $resultado = $this->service->finalizarVendaDoCarrinho($request->client);

            if (!$resultado['ok']) {
                return response()->json([
                    'success' => false,
                    'message' => $resultado['mensagem'] ?? 'Falha ao finalizar',
                    'detalhes' => $resultado['faltantes'] ?? [],
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Venda registrada com sucesso!',
                'order' => $resultado,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar a venda: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function historicoVendas()
    {
        return view('vendas.historico_vendas');
    }
}
