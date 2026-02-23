<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Services\AppSettingService;
use App\Services\VendaService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class VendaController extends Controller
{
    public function __construct(
        private VendaService $service,
        private AppSettingService $settings
    )
    {
        //
    }

    public function vendas(): InertiaResponse
    {
        return Inertia::render('Sales/Index', [
            'requireClient' => $this->settings->getBool(AppSettingService::KEY_SALES_REQUIRE_CLIENT, true),
            'vendas' => fn () => Venda::query()
                ->with([
                    'usuario:id,name',
                    'produto:id_produto,nome_produto,cod_produto',
                ])
                ->latest()
                ->paginate(10)
                ->through(function (Venda $venda): array {
                    return [
                        'id' => $venda->id,
                        'nome_produto' => (string) ($venda->nome_produto ?: optional($venda->produto)->nome_produto),
                        'preco_venda' => (float) $venda->preco_venda,
                        'cod_produto' => (string) ($venda->cod_produto ?: optional($venda->produto)->cod_produto),
                        'quantidade' => (int) $venda->quantidade,
                        'vendedor' => (string) optional($venda->usuario)->name,
                        'data' => optional($venda->created_at)->format('Y-m-d H:i:s'),
                    ];
                }),
        ]);
    }

    public function buscarProduto(Request $request)
    {
        $request->validate([
            'client' => 'nullable|string|max:20',
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
                $request->input('codigo_produto'),
                $request->input('client')
            );

            return response()->json([
                'success' => true,
                'produto' => $dados['produto'] ?? null,
                'opcoes' => $dados['opcoes'] ?? [],
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
            'itens.*.id_estoque' => 'nullable|integer|exists:estoques,id_estoque',
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
        $requireClient = $this->requireClient();
        $request->validate([
            'client' => $requireClient
                ? 'required|string|max:20'
                : 'nullable|string|max:20',
        ]);

        $client = $this->resolveClient($request, $requireClient);
        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Informe o cliente para carregar o carrinho.',
            ], 422);
        }

        $cart = $this->service->obterCarrinho($client);

        return response()->json([
            'success' => true,
            'cart' => $cart->toArray(),
        ]);
    }

    public function adicionarItem(Request $request)
    {
        $requireClient = $this->requireClient();
        $request->validate([
            'client' => $requireClient
                ? 'required|string|max:20'
                : 'nullable|string|max:20',
            'id_produto' => 'required|integer',
            'id_estoque' => 'nullable|integer|exists:estoques,id_estoque',
            'quantidade' => 'required|integer|min:1',
        ]);

        try {
            $client = $this->resolveClient($request, $requireClient);
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Informe o cliente para adicionar itens.',
                ], 422);
            }

            $cart = $this->service->adicionarItem(
                $client,
                (int) $request->id_produto,
                (int) $request->quantidade,
                $request->filled('id_estoque') ? (int) $request->id_estoque : null
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
        $requireClient = $this->requireClient();
        $request->validate([
            'client' => $requireClient
                ? 'required|string|max:20'
                : 'nullable|string|max:20',
            'cart_item_id' => 'required|integer',
            'quantidade' => 'required|integer|min:0',
        ]);

        try {
            $client = $this->resolveClient($request, $requireClient);
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Informe o cliente para atualizar itens.',
                ], 422);
            }

            $cart = $this->service->atualizarQuantidadeItem(
                $client,
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
        $requireClient = $this->requireClient();
        $request->validate([
            'client' => $requireClient
                ? 'required|string|max:20'
                : 'nullable|string|max:20',
            'cart_item_id' => 'required|integer',
        ]);

        try {
            $client = $this->resolveClient($request, $requireClient);
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Informe o cliente para remover itens.',
                ], 422);
            }

            $cart = $this->service->removerItem(
                $client,
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
        $requireClient = $this->requireClient();
        $request->validate([
            'client' => $requireClient
                ? 'required|string|max:20'
                : 'nullable|string|max:20',
        ]);

        try {
            $client = $this->resolveClient($request, $requireClient);
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Informe o cliente antes de finalizar a venda.',
                ], 422);
            }

            $resultado = $this->service->finalizarVendaDoCarrinho($client);

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
        return redirect()->route('vendas.venda');
    }

    private function requireClient(): bool
    {
        return $this->settings->getBool(AppSettingService::KEY_SALES_REQUIRE_CLIENT, true);
    }

    private function resolveClient(Request $request, bool $requireClient): ?string
    {
        $client = trim((string) $request->input('client', ''));
        if ($client !== '') {
            return $client;
        }

        if ($requireClient) {
            return null;
        }

        $userId = auth()->id();
        if (!$userId) {
            return null;
        }

        $key = AppSettingService::ANON_CLIENT_PREFIX . $userId;
        return strlen($key) > 20 ? substr($key, 0, 20) : $key;
    }
}
